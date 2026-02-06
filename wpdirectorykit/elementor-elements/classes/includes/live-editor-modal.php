<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wdk-live-editor-iframe-modal">
	<div class="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox elementor-templates-modal wdk-dynamic-content-modal" id="elementor-template-pa-live-editor-modal-container" style="display:none">
		<div class="dialog-widget-content dialog-lightbox-widget-content">
			<div class="dialog-header dialog-lightbox-header">
				<div class="elementor-templates-modal__header">
					<div class="elementor-templates-modal__header__logo-area">
						<div class="elementor-templates-modal__header__logo">
							<span class="elementor-templates-modal__header__logo__title"><?php esc_html_e( 'Template Editor', 'wdk-addons-for-elementor' ); ?></span>
							<div class="wdk-live-editor-title">
								<input type="text" id="wdk-live-temp-title" name="wdkLiveTempTitle" placeholder="Enter template name...">
								<button id="pa-insert-live-temp" class="elementor-template-library-template-action wdk-template-insert elementor-button elementor-button-success" ><?php esc_html_e( 'Save Template', 'wdk-addons-for-elementor' ); ?></button>
							</div>
						</div>
					</div>
					<div class="elementor-templates-modal__header__items-area">
						<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
							<i class="eicon-close" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'wdk-addons-for-elementor' ); ?>"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Close', 'wdk-addons-for-elementor' ); ?></span>
						</div>
						<div class="elementor-templates-modal__header__expand  elementor-templates-modal__header__item wdk-expand">
							<i class="eicon-frame-expand" aria-hidden="true" title="<?php echo esc_attr__( 'Expand', 'wdk-addons-for-elementor' ); ?>"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Expand', 'wdk-addons-for-elementor' ); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="dialog-message dialog-lightbox-message">
				<div class="dialog-content dialog-lightbox-content" style="display: block;">
					<div id="elementor-template-library-templates" data-template-source="remote">

						<div id="elementor-template-library-templates-container">
							<iframe id="pa-live-editor-control-iframe"></iframe>
						</div>
					</div>
				</div>
				<div class="dialog-loading dialog-lightbox-loading" style="display: block;">
					<div id="elementor-template-library-loading">
						<div class="elementor-loader-wrapper">
							<div class="elementor-loader">
								<div class="elementor-loader-boxes">
									<div class="elementor-loader-box"></div>
									<div class="elementor-loader-box"></div>
									<div class="elementor-loader-box"></div>
									<div class="elementor-loader-box"></div>
								</div>
							</div>
							<div class="elementor-loading-title"><?php esc_html_e( 'Loading', 'wdk-addons-for-elementor' ); ?></div>
						</div>
					</div>
				</div>
			</div>
			<div class="dialog-buttons-wrapper dialog-lightbox-buttons-wrapper"></div>
		</div>
	</div>
</div>