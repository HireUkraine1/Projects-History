<div id="simplecheckout_login" style="padding-top: 0;">
    <div class="simplecheckout-warning-block" style="margin-top: -10px; font-size: 12px; padding: 5px 10px 5px 33px; <?php if (!$error_login) { ?> visibility:hidden;<?php } ?>"><?php echo $error_login ?></div>
    <table class="simplecheckout-login" <?php if (!$error_login) { ?>style="margin-top: 15px;"<?php } ?>>
        <tr>
            <td class="simplecheckout-login-left"><?php echo $entry_email; ?></td>
            <td class="simplecheckout-login-right"><input class="form-control" type="text" name="email" value="<?php echo $email; ?>" /></td>
        </tr>
        <tr>
            <td style="padding-top: 10px;" class="simplecheckout-login-left"><?php echo $entry_password; ?></td>
            <td style="padding-top: 10px;" class="simplecheckout-login-right"><input class="form-control" type="password" name="password" value="" /></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; padding: 10px 0;" class="simplecheckout-login-right"><a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;" class="simplecheckout-login-right buttons"><a id="simplecheckout_button_login" onclick="simplecheckout_login()" class="buy nbut"><span><?php echo $button_login; ?></span></a></td>
        </tr>
    </table>
</div>
<script type='text/javascript'>
$('.simplecheckout-login input').keydown(function(e) {
    if (e.keyCode == 13) {
        simplecheckout_login();
    }
});
</script>
<?php if ($redirect) { ?>
<script type='text/javascript'>
location = '<?php echo $redirect ?>';
</script>
<?php } ?>
<!-- You can add here the social engine code for login in the simplecheckout_customer.tpl -->
    