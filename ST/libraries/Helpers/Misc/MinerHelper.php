<?php

use Dandelionmood\LastFm\LastFm;
use Guzzle\Http\Client;
use Illuminate\Console\Command;
use MusicBrainz\Filters\ArtistFilter;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MinerHelper
{

    //NOT USED AS OF RIGHT NOW
    public static function insert_performer($tnPerformer = null, $refresh = false)
    {
        //create last fm instance
        $lfmConfig = Config::get("lastfm.lfmkeys");
        $lfm = new  \Dandelionmood\LastFm\LastFm($lfmConfig['key'], $lfmConfig['secret']);

        $mbzConfig = Config::get("musicbrainz");
        $adapter = new GuzzleHttpAdapter(new Client());
        $brainz = new MusicBrainz($adapter, $mbzConfig['username'], $mbzConfig['password']);
        $brainz->setUserAgent('site', '0.2', 'http://site.com');

        $slConfig = Config::get("setlist");
        $sl = new SetList($slConfig);

        // echo "\n\r TN: $tnPerformer->Description";
        $slug = StringHelper::create_slug($tnPerformer->Description);

        //what genre is it
        $genre = MinerHelper::insert_genre($tnPerformer->Category);
        // echo "\n\r Genre is : ". $genre->genre;

        //do we have performer in the database
        $existingPerformer = Performer::where('slug', $slug)->orWhere('tn_id', $tnPerformer->ID)->first();

        if ($existingPerformer): //we already have this in OUR database
            // echo " - FOUND:";

            $performerDetails = PerformerDetails::where(['performer_id' => $existingPerformer->id])->first();
            if (!$performerDetails): //and most likely we do not have it, but still
                $performerDetails = new PerformerDetails;
            endif;

            $performerSet = PerformerSets::where(["performer_id" => $existingPerformer->id])->first();


            if (!$performerSet):
                $performerSet = new PerformerSets;
            endif;

            if (!$existingPerformer->genre_id) $existingPerformer->genre_id = $genre->id;
            //TODO: write code to act on PerfromerDetails if there is one in our mysql db
            if ($refresh):
                // echo "\n\r\t REFRESHING DATA....\n\r";
                //create staging ground for refresh only conditions
                if (DB::table('tmp_album_stage')->where('performer_id', $existingPerformer->id)->count() == 0)
                    DB::table('tmp_album_stage')->insert(['performer_id' => $existingPerformer->id]);
                //SETLIST BLOCK
                if ($refresh['sl']):
                    try {
                        // echo "\n\r\tSL - ";
                        $setlistUS = $sl->search_setlist($existingPerformer->mbz_id, 'US');
                        $setlistCA = $sl->search_setlist($existingPerformer->mbz_id, 'CA');
                        // DebugHelper::pdd($artistsets, false);
                        $setlists = array_merge($setlistUS, $setlistCA);
                        $performerSet->performer = $existingPerformer->name;
                        $performerSet->performer_id = $existingPerformer->id;
                        $performerSet->mbz_id = $existingPerformer->mbz_id;
                        $performerSet->sets = $setlists;
                        $performerSet->save();
                        // echo " OK\n\r";
                    } catch (Exception $e) {
                        // echo $e->getMessage();
                        // echo $e->getTraceAsString().;
                        echo "\nERROR OCCURRED: {$existingPerformer->name}" . $e->getMessage();
                        // var_dump($performerSet);
                        Log::error("Error on assigning set list.");
                    }
                endif;

                if ($refresh['lfm']):
                    try {

                        $lfmData = $lfm->artist_getInfo(['artist' => $tnPerformer->Description]);
                        $existingPerformer->plays = $lfmData->artist->stats->playcount;
                        $existingPerformer->listeners = $lfmData->artist->stats->listeners;
                        $existingPerformer->genre_id = $genre->id;
                        $existingPerformer->save();
                        // echo "\tLFM - ";

                        //refresh details
                        //after we inserted performer, JUST in case let's try to grab on from mongo, WHAT if we have it..
                        $performerDetails = PerformerDetails::where(['performer_id' => $existingPerformer->id])->first();

                        if (!$performerDetails): //and most likely we do not have it, but still
                            // echo "\n\t NO DETAILS... creating...";
                            $performerDetails = new PerformerDetails;
                            $performerDetails->performer_id = $existingPerformer->id;
                        endif;

                        // echo "\tSimilar: ";
                        //lets get similar artists for diz guy

                        if ($existingPerformer->mbz_id):
                            $lfmArgs = ['mbid' => $existingPerformer->mbz_id];
                        else:
                            $lfmArgs = ['artist' => $existingPerformer->name]; //this is not ideal..but what can we do
                        endif;

                        try {
                            //get similar artists
                            $similar = $lfm->artist_getSimilar($lfmArgs);
                            $performerDetails->similar = MinerHelper::_parseLfmSimilar($similar->similarartists);
                            // echo "OK";
                        } catch (Exception $e) {
                            echo "\n" . $e->getMessage() . " ({$existingPerformer->name})";
                            // echo $e->getTraceAsString();
                            // Log::info("$existingPerformer->name : Failed to get similar performers with: ".$e->getMessage());
                            $performerDetails->similar = [];
                        }
                        // echo "\tTAGS: ";
                        try {
                            //get tags
                            $tags = $lfm->artist_getTopTags($lfmArgs);
                            $performerDetails->tags = MinerHelper::_parseLfmTags($tags->toptags);
                            // echo " OK";
                        } catch (Exception $e) {
                            echo "\n" . $e->getMessage() . " ({$existingPerformer->name})";

                            // Log::info("$existingPerformer->name : Failed to get tags for performers with: ".$e->getMessage());
                            $performerDetails->tags = [];
                        }
                        // echo "\tIMG: ";
                        try {
                            $imgs = VarsHelper::parse_lfm_images($lfmData->artist->image);

                            if ($imgs):
                                foreach ($imgs as $key => $img):
                                    $im = new Image;
                                    $type = "lfm";
                                    $size = $key;

                                    $name = StringHelper::create_slug($existingPerformer->id . " " . $existingPerformer->slug . " " . $key);
                                    $s3path = S3Helper::putRemote($img, "performers/", $name);

                                    $im->path = ($s3path) ? $s3path : '';
                                    $im->save();
                                    $existingPerformer->images()->detach($im->id);
                                    $existingPerformer->images()->attach($im, array('type' => $type, 'size' => $size));
                                    //    DebugHelper::pdd($im->toArray());
                                endforeach;
                            // echo " - OK ";
                            else:
                                // echo " - NOT OK";
                            endif;
                        } catch (Exception $e) {
                            echo "\n" . $e->getMessage() . " ({$existingPerformer->name})";

                            // echo $e->getMessage();
                            // echo $e->getTraceAsString();
                            // Log::warning("$existingPerformer->name : Set up performer pictures failed with ".$e->getMessage());
                        }
                        //save refresh
                        try {
                            $performerDetails->save(); //and mongod that shit

                            // "\tDETAILS OK (MONGO)";
                        } catch (Exception $e) {
                            echo "\nPERFORMER DETAILS: No Details for $tnPerformer->Description \n\n";
                            // echo $e->getMessage();
                            // echo $e->getTraceAsString();
                            // var_dump($performerDetails);
                        }
                    } catch (Exception $e) {
                        echo "\nFailed on update " . $existingPerformer->name . " with error: " . $e->getMessage();
                        // Log::warning("Failed on update ".$existingPerformer->name." with error: ".$e->getMessage());
                        // echo $e->getMessage();
                        // echo $e->getTraceAsString();
                    }
                endif; //if lfm refresh
            endif; //global refresh bblock
        else:
            //inserting performer based on TN data (new guy)
            $insertPerformer = new Performer;
            $insertPerformer->name = $tnPerformer->Description;
            $insertPerformer->slug = $slug;
            $insertPerformer->tn_id = $tnPerformer->ID;
            $insertPerformer->status = 1;
            $insertPerformer->genre_id = $genre->id;

            //doing LFM data
            // echo "\tLFM.....";
            try {

                $lfmData = $lfm->artist_getInfo(['artist' => $tnPerformer->Description]);

                $insertPerformer->plays = $lfmData->artist->stats->playcount;
                $insertPerformer->listeners = $lfmData->artist->stats->listeners;
                $insertPerformer->bio_summary = strip_tags($lfmData->artist->bio->summary);
                $insertPerformer->bio = strip_tags($lfmData->artist->bio->content);
                $insertPerformer->mbz_id = $lfmData->artist->mbid;
                $imgs = VarsHelper::parse_lfm_images($lfmData->artist->image);
                // echo "OK";

            } catch (Exception $e) {
                echo "\n$insertPerformer->name : Get LFM data failed with " . $e->getMessage();
                $insertPerformer->plays = 0;
                $insertPerformer->listeners = 0;
                $insertPerformer->bio_summary = null;
                $insertPerformer->bio = null;
                $insertPerformer->mbz_id = null;
                // echo $e->getMessage();
                // echo $e->getTraceAsString();
            }
            // echo " MBZID: ".$insertPerformer->mbz_id;
            //get musicBrainz
            // echo "\tMBZ......";
            try {
                if ($insertPerformer->mbz_id):
                    //lookup by mbz
                    // echo ".... searching by id...";
                    $args = ['arid' => $insertPerformer->mbz_id];
                else:
                    // echo "...searching by name ...";
                    $args = ["artist" => $insertPerformer->name];
                endif;

                //see what cat dragged in
                $result = $brainz->search(new ArtistFilter($args));

                if (!isset($result[0])): //last chance
                    // echo "WASNT SET";
                    $args = ["artist" => $insertPerformer->name];
                    $result = $brainz->search(new ArtistFilter($args));
                    $artist = $result[0];

                    $insertPerformer->type = $artist->getType();
                    $insertPerformer->gender = $artist->getGender();
                    $insertPerformer->formed = $artist->gesaleeginDate();
                    $insertPerformer->ended = $artist->getEndDate();
                    $insertPerformer->disambiguation = $artist->getDisambiguation();
                    $insertPerformer->country = $artist->getCountry();
                    $insertPerformer->mbz_id = $artist->id;
                else:
                    // echo "WAS SET";
                    $artist = $result[0];

                    $insertPerformer->type = $artist->getType();
                    $insertPerformer->gender = $artist->getGender();
                    $insertPerformer->formed = $artist->gesaleeginDate();
                    $insertPerformer->ended = $artist->getEndDate();
                    $insertPerformer->disambiguation = $artist->getDisambiguation();
                    $insertPerformer->country = $artist->getCountry();
                    $insertPerformer->mbz_id = $artist->id;
                endif;

                if (!$insertPerformer->mbz_id):
                    $insertPerformer->mbz_id = $artist->id;
                endif;
                // echo "OK";
            } catch (Exception $e) {
                echo "\n$insertPerformer->name : Get MBZ data failed with " . $e->getMessage();
                //     dd($insertPerformer);
                // echo " failed because ... ".$e->getMessage()." and ".$e->getTraceAsString();
                $insertPerformer->type = null;
                $insertPerformer->gender = null;
                $insertPerformer->formed = null;
                $insertPerformer->ended = null;
                $insertPerformer->disambiguation = null;
                $insertPerformer->country = null;
                $insertPerformer->mbz_id = '';
                // echo $e->getMessage();
                // echo $e->getTraceAsString();
            }
            try {
                $insertPerformer->save();
                if (DB::table('tmp_album_stage')->where('performer_id', $insertPerformer->id)->count() == 0)
                    DB::table('tmp_album_stage')->insert(['performer_id' => $insertPerformer->id]);
                // echo "\tSAVED!";


            } catch (Exception $e) {
                echo "\nFAILED: {$insertPerformer->name} ({$insertPerformer->slug}) - " . substr($e->getMessage(), 0, 111);
                return false;
            }


            //setlist
            $performerSet = PerformerSets::where(["performer_id" => $insertPerformer->id])->first();
            if (!$performerSet):
                $performerSet = new PerformerSets;
            endif;

            try {
                $setlistUS = $sl->search_setlist($insertPerformer->mbz_id, 'US');
                $setlistCA = $sl->search_setlist($insertPerformer->mbz_id, 'CA');
                // DebugHelper::pdd($artistsets, false);
                $setlists = array_merge($setlistUS, $setlistCA);

                $performerSet->performer = $insertPerformer->name;
                $performerSet->performer_id = $insertPerformer->id;
                $performerSet->mbz_id = $insertPerformer->mbz_id;
                $performerSet->sets = $setlists;
                $performerSet->save();
                // echo "OK\n\r";
            } catch (Exception $e) {
                echo "\n({$insertPerformer->name}) " . $e->getMessage();
                // echo $e->getTraceAsString();
                // var_dump($performerSet);
                // Log::error("Error on assigning set list.");
            }

            //after we inserted performer, JUST in case let's try to grab on from mongo, WHAT if we have it..
            $performerDetails = PerformerDetails::where(['performer_id' => $insertPerformer->id])->first();

            if (!$performerDetails): //and most likely we do not have it, but still
                $performerDetails = new PerformerDetails;
                $performerDetails->performer_id = $insertPerformer->id;
            endif;

            // echo "\n\rSimilar: ";
            //lets get similar artists for diz guy

            if ($insertPerformer->mbz_id):
                $lfmArgs = ['mbid' => $insertPerformer->mbz_id];
            else:
                $lfmArgs = ['artist' => $insertPerformer->name]; //this is not ideal..but what can we do
            endif;

            try {
                //get similar artists
                $similar = $lfm->artist_getSimilar($lfmArgs);
                $performerDetails->similar = MinerHelper::_parseLfmSimilar($similar->similarartists);
                // echo "OK";
            } catch (Exception $e) {
                // echo $e->getMessage();
                // echo $e->getTraceAsString();
                echo "\n$insertPerformer->name : Failed to get similar performers with: " . $e->getMessage();
                $performerDetails->similar = [];
            }
            // echo "\tTAGS: ";
            try {
                //get tags
                $tags = $lfm->artist_getTopTags($lfmArgs);
                $performerDetails->tags = MinerHelper::_parseLfmTags($tags->toptags);
                // echo " OK";
            } catch (Exception $e) {
                // echo $e->getMessage();
                // echo $e->getTraceAsString();
                echo "\n$insertPerformer->name : Failed to get tags for performers with: " . $e->getMessage();
                $performerDetails->tags = [];
            }
            // echo "\tIMG: ";
            try {
                if ($imgs):
                    foreach ($imgs as $key => $img):
                        $im = new Image;
                        $type = "lfm";
                        $size = $key;

                        $name = StringHelper::create_slug($insertPerformer->id . " " . $insertPerformer->slug . " " . $key);
                        $s3path = S3Helper::putRemote($img, "performers/", $name);

                        $im->path = ($s3path) ? $s3path : '';
                        $im->save();
                        $insertPerformer->images()->detach($im->id);
                        $insertPerformer->images()->attach($im, array('type' => $type, 'size' => $size));
                        //    DebugHelper::pdd($im->toArray());
                    endforeach;
                // echo " OK";
                else:
                    echo " NO IMGS";
                endif;
            } catch (Exception $e) {
                echo "\n$insertPerformer->name : Set up performer pictures failed with " . $e->getMessage();
            }
            try {
                $performerDetails->save(); //and mongod that shit
                // "\n\r DETAILS OK (MONGO)";
            } catch (Exception $e) {
                echo $e->getMessage();
                // echo $e->getTraceAsString();
                // var_dump($performerDetails);
            }
        endif; //ifnew

    }

    public static function insert_genre($tnGenre = null, $refresh = false)
    {
        if ($tnGenre):
            $properName = ucwords(strtolower($tnGenre->ChildCategoryDescription));
            $grandChild = str_replace('-', '', $tnGenre->GrandchildCategoryDescription);
            if ($grandChild):
                $properName .= " - " . ucwords(strtolower($grandChild));
            endif;
            $genre = Genre::firstOrCreate([
                'slug' => StringHelper::create_slug($properName),
                'tn_parent_category_id' => $tnGenre->ParentCategoryID,
                'tn_child_category_id' => $tnGenre->ChildCategoryID,
                'tn_grandchild_category_id' => $tnGenre->GrandchildCategoryID,
            ]);
            $genre->genre = $properName;
            $genre->save();
            return $genre;
        endif;
        return null;
    }

    private static function _parseLfmSimilar($object)
    {
        $returnValue = [];
        $data = [];
        if (isset($object->artist) && $object->artist):
            foreach ($object->artist as $similar) {
                $data = [
                    'name' => $similar->name,
                    'match' => $similar->match,
                    'mbz_id' => $similar->mbid
                ];
                array_push($returnValue, $data);
            }
        endif;
        return $returnValue;
    }

    private static function _parseLfmTags($object)
    {
        $returnValue = [];
        $data = [];
        if (isset($object->tag) && $object->tag):
            foreach ($object->tag as $tag) {
                $data = [
                    'tag' => $tag->name,
                    'count' => $tag->count,
                ];
                array_push($returnValue, $data);
            }
        endif;
        return $returnValue;
    }


}