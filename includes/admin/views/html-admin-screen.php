<div class="wrap woocommerce">
    <h2 class="nav-tab-wrapper"><?php echo __('Menu settings to hide by role', 'role-wc'); ?></h2>
    <div>
        <form id="jp4wc-setting-form" method="post" action="">
        <?php
        $page = 'role_wc';
        //Display Setting Screen
        settings_fields( $page );
        do_settings_sections( $page );
        ?>
            <p class="submit">
                <?php submit_button( '', 'primary', 'save_'.$page, false ); ?>
            </p>
        </form>
    </div>
</div>
