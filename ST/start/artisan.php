<?php
/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new GetGeoCommand);
Artisan::add(new GetPerformerDataCommand);
Artisan::add(new GetVenueDataCommand);
Artisan::add(new GetAlbumTrackDataCommand);
Artisan::add(new UpdateCountsCommand);
Artisan::add(new AssignGenresCommand);
Artisan::add(new SearchIndexCommand);
Artisan::add(new RunDailyCommand);
Artisan::add(new GetTicketsCommand);
Artisan::add(new TempCommand);
Artisan::add(new SpinCommand);
Artisan::add(new SendNoticesCommand);
Artisan::add(new DeleteAllAlbumsInfoCommand);
Artisan::add(new GetalbumInfoCommand);
Artisan::add(new GetInfoTicketsStatisticsCommand);
Artisan::add(new SendMailTrackConcert);