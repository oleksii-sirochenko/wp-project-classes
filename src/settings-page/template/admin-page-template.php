<div class="wrap">
  <form action="options.php" method="post" enctype="multipart/form-data">
		<?php settings_fields( $option_key ); ?>
    <table class="form-table">
      <tr>
        <th></th>
        <td></td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>"/>
    </p>
  </form>
</div>