<?php

defined( 'ABSPATH' ) || exit;

?>
<div class="ur-input-type-text ur-admin-template">
	<div class="ur-label">
		<label>
		<?php
			// @phpstan-ignore-next-line
			echo esc_html( $this->get_general_setting_data( 'label' ) );
		?>
		</label>
	</div>
	<!-- Do not skip this DIV, its field-key is used in the saving process. -->
	<div class="ur-field" data-field-key="prosopo_procaptcha"></div>
</div>
