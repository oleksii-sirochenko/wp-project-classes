<h2><?php echo $page_title; ?></h2>
<table class="form-table">
    <tr>
        <th>value_1</th>
        <td><input type="text" name="<?php echo $option_key; ?>[value_1]" value="<?php
            if ( isset( $options['value_1'] ) ) {
                echo $options['value_1'];
            }
            ?>"></td>
    </tr>
</table>