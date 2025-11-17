<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$opts = get_option( 'roibot_settings', array() );
?>
<div class="wrap">
  <h1>Roibot Settings</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'roibot_settings_group' ); ?>
    <table class="form-table" role="presentation">
      <tr>
        <th scope="row">Activate site-wide</th>
        <td><label><input type="checkbox" name="roibot_settings[sitewide_enable]" value="1" <?php checked( 1, $opts['sitewide_enable'] ?? 0 ); ?>> Show the chatbot on all public pages (in footer)</label></td>
      </tr>
      <tr>
        <th scope="row">Header name</th>
        <td><input type="text" class="regular-text" name="roibot_settings[header_name]" value="<?php echo esc_attr( $opts['header_name'] ?? 'ROI' ); ?>"></td>
      </tr>
      <tr>
        <th scope="row">Header avatars</th>
          <td>
            <div class="roibot-media-row">
              <input type="text" id="roibot_avatar1" class="regular-text" name="roibot_settings[avatar1]" value="<?php echo esc_attr( $opts['avatar1'] ?? '' ); ?>">
              <button type="button" class="button roibot-media" data-target="#roibot_avatar1" data-preview="#roibot_avatar1_preview">Choose</button>
              <img id="roibot_avatar1_preview" src="<?php echo esc_url( $opts['avatar1'] ?? '' ); ?>" style="max-height:40px; <?php echo empty( $opts['avatar1'] ) ? 'display:none' : ''; ?>">
            </div>
            <div class="roibot-media-row">
              <input type="text" id="roibot_avatar2" class="regular-text" name="roibot_settings[avatar2]" value="<?php echo esc_attr( $opts['avatar2'] ?? '' ); ?>">
              <button type="button" class="button roibot-media" data-target="#roibot_avatar2" data-preview="#roibot_avatar2_preview">Choose</button>
              <img id="roibot_avatar2_preview" src="<?php echo esc_url( $opts['avatar2'] ?? '' ); ?>" style="max-height:40px; <?php echo empty( $opts['avatar2'] ) ? 'display:none' : ''; ?>">
            </div>
          </td>
          </tr>
      <tr>
        <th scope="row">Popup text</th>
        <td><textarea class="large-text" rows="3" name="roibot_settings[popup_text]"><?php echo esc_textarea( $opts['popup_text'] ?? 'We are here to take the weight off your shoulders. Tell us about your challenges.' ); ?></textarea></td>
      </tr>
      <tr>
        <th scope="row">Welcome text</th>
        <td><textarea class="large-text" rows="3" name="roibot_settings[welcome_text]"><?php echo esc_textarea( $opts['welcome_text'] ?? '👋 Hi, how can we help you? You can start chatting right away.' ); ?></textarea></td>
      </tr>
      <tr>
        <th scope="row">Brand colors</th>
        <td>
          <div class="roibot-colors">
            <label>Primary<br><input type="text" class="color-field" name="roibot_settings[brand_primary]" value="<?php echo esc_attr( $opts['brand_primary'] ?? '#0EA5E9' ); ?>"></label>
            <label>Accent<br><input type="text" class="color-field" name="roibot_settings[brand_accent]"  value="<?php echo esc_attr( $opts['brand_accent']  ?? '#22C55E' ); ?>"></label>
            <label>Header BG<br><input type="text" class="color-field" name="roibot_settings[brand_bg]"      value="<?php echo esc_attr( $opts['brand_bg']      ?? '#0f172a' ); ?>"></label>
            <label>Text<br><input type="text" class="color-field" name="roibot_settings[brand_text]"    value="<?php echo esc_attr( $opts['brand_text']    ?? '#0f172a' ); ?>"></label>
          </div>
        </td>
      </tr>
      <tr>
        <th scope="row">News items</th>
        <td>
          <table class="widefat" id="roibot-news-table">
            <thead>
              <tr><th>Title</th><th>URL</th><th style="width:90px"></th></tr>
            </thead>
            <tbody>
              <?php $rows = ! empty( $opts['news_items'] ) && is_array( $opts['news_items'] ) ? $opts['news_items'] : array(); ?>
              <?php foreach ( $rows as $i => $it ) : ?>
                <tr>
                  <td><input type="text" class="regular-text" name="roibot_settings[news_items][<?php echo esc_attr($i); ?>][title]" value="<?php echo esc_attr( $it['title'] ?? '' ); ?>"></td>
                  <td><input type="url"  class="regular-text" name="roibot_settings[news_items][<?php echo esc_attr($i); ?>][url]"   value="<?php echo esc_url(  $it['url']   ?? '' ); ?>" placeholder="https://"></td>
                  <td><button type="button" class="button link-delete">Remove</button></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <p><button type="button" class="button button-secondary" id="roibot-add-news">+ Add item</button></p>

          <script type="text/html" id="tmpl-roibot-news-row">
            <tr>
              <td><input type="text" class="regular-text" name="roibot_settings[news_items][{{index}}][title]" value=""></td>
              <td><input type="url"  class="regular-text" name="roibot_settings[news_items][{{index}}][url]"   value="" placeholder="https://"></td>
              <td><button type="button" class="button link-delete">Remove</button></td>
            </tr>
          </script>
        </td>
      </tr>

    </table>
    <?php submit_button(); ?>
  </form>
</div>
