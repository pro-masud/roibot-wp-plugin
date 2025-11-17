
<?php
class BNI_Chatbot_Gemini_SingleFile_V1 {
    const OPT_GROUP = 'bni_chatbot_gemini_group';
    const OPT_NAME  = 'bni_chatbot_gemini_options';
    const NONCE_ACTION = 'wp_rest';

    public function __construct() {
        add_shortcode('bni_chatbot', [$this, 'shortcode']);
        add_action('rest_api_init',  [$this, 'register_rest']);
        add_action('admin_menu',     [$this, 'settings_menu']);
        add_action('admin_init',     [$this, 'register_settings']);
    }

    /* ---------- Shortcode (HTML + inline CSS/JS) ---------- */
    public function shortcode($atts = []) {
        $opts = get_option(self::OPT_NAME, [
            'base_url' => 'https://generativelanguage.googleapis.com/v1',
            'api_key'  => '',
            'model'    => 'gemini-1.5-flash-latest',
            'system'   => 'You are a helpful assistant.',
        ]);

        if ( empty($opts['api_key']) ) {
            return '<div style="color:#b91c1c">BNI Chatbot: Settings → BNI Chatbot (Gemini) এ গিয়ে <b>Gemini API Key</b> দিন।</div>';
        }

        $rest_url = esc_url_raw( rest_url('bni/v1/chat') );
        $nonce    = wp_create_nonce(self::NONCE_ACTION);

        ob_start();
        ?>
        <style>
            .bni-chatbot{max-width:680px;margin:0;border:1px solid #e2e8f0;border-radius:0px;overflow:hidden;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
            .bni-head{padding:.75rem 1rem;background:#0ea5e9;color:#fff;font-weight:600;display:flex;justify-content:space-between;align-items:center}
            .bni-head .actions{display:flex;gap:.5rem}
            .bni-body{padding:1rem;background:#fff;height:250px;overflow:auto;display:flex;flex-direction:column}
            .bni-msg{margin:.5rem 0;padding:.5rem .75rem;border-radius:10px;display:inline-block;max-width:85%;white-space:pre-wrap}
            .bni-chat-user{background:#e5f3ff;align-self:flex-end}
            .bni-bot{background:#f1f5f9;align-self:flex-start}
            .bni-input{display:flex;gap:.5rem;padding:.75rem;background:#f8fafc;border-top:1px solid #e2e8f0}
            .bni-input input{flex:1;padding:.6rem .75rem;border:1px solid #cbd5e1;border-radius:8px}
            .bni-input button{padding:.6rem .9rem;border:0;border-radius:8px;background:#CF2030;color:#fff;font-weight:600;cursor:pointer}
            .bni-input button:disabled{opacity:.6;cursor:not-allowed}
            .bni-ghost{opacity:.7}
            .bni-input input:focus{
            border: 1px solid #CF2030 !important;
            }
            .bni-input input:focus-visible{
                outline: 1px solid #CF2030 !important;
            }
        </style>

<div class="bni-chatbot" data-bni="widget">
  <div class="bni-body" id="bni-body" aria-live="polite"></div>
  <div class="bni-input">
    <input id="bni-input" type="text" placeholder="Type your message…" />
    <button id="bni-send">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1792 1792"><path fill="currentColor" d="M1764 11q33 24 27 64l-256 1536q-5 29-32 45q-14 8-31 8q-11 0-24-5l-453-185l-242 295q-18 23-49 23q-13 0-22-4q-19-7-30.5-23.5T640 1728v-349l864-1059l-1069 925l-395-162q-37-14-40-55q-2-40 32-59L1696 9q15-9 32-9q20 0 36 11"/></svg>
    </button>
  </div>
</div>

<script>
(function(){
  var REST_URL = <?php echo json_encode($rest_url); ?>;
  var NONCE    = <?php echo json_encode($nonce); ?>;

  function el(id){return document.getElementById(id)}

  // ---------- Local persistence ----------
  var STORAGE_KEY = 'bni_chat_history_v1';

  function loadHistory(){
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch(_){ return []; }
  }
  function saveHistory(hist){
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(hist.slice(-200))); } catch(_){}
  }
  function renderHistory(){
    var hist = loadHistory();
    hist.forEach(function(item){ addMsg(item.text, item.who, true); });
  }
  function clearHistoryUI(){
    try { localStorage.removeItem(STORAGE_KEY); } catch(_){}
    var body = el('bni-body'); if (body) body.innerHTML = '';
  }

  // ---------- UI helpers ----------
  function addMsg(text, who, skipPersist){
    var body = el('bni-body');
    var div = document.createElement('div');
    div.className = 'bni-msg ' + (who === 'user' ? 'bni-chat-user' : 'bni-bot');
    div.textContent = text;
    body.appendChild(div);
    body.scrollTop = body.scrollHeight;

    if (!skipPersist) {
      var hist = loadHistory();
      hist.push({ text: String(text), who: who, t: Date.now() });
      saveHistory(hist);
    }
  }

  function setBusy(b){
    var send = el('bni-send');
    if (send) send.disabled = !!b;
    var input = el('bni-input');
    if (input) input.classList.toggle('bni-ghost', !!b);
  }

  // ---------- Network ----------
  async function sendMessage(msg){
    var res, raw, data;
    var hist = loadHistory(); // include prior turns
    try {
      res = await fetch(REST_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': NONCE
        },
        body: JSON.stringify({ message: msg, history: hist })
      });
      raw = await res.text();
      try { data = JSON.parse(raw); } catch(_){ data = { error: 'Non-JSON response', body: raw }; }
    } catch(e){
      addMsg('[Network error] ' + e.message, 'bot');
      return;
    }

    if (res.ok) {
      var reply = (data && data.reply) ? data.reply : 'No reply';
      addMsg(reply, 'bot');
    } else {
      var extra = [];
      extra.push('status=' + (data.status || res.status));
      if (data.body || raw) extra.push('body=' + String(data.body || raw).slice(0, 300));
      addMsg('[Error] ' + (data.error || 'Unknown error') + ' (' + extra.join(', ') + ')', 'bot');
    }
  }

  // ---------- Boot ----------
  function boot(){
    var input = el('bni-input');
    var send  = el('bni-send');
    var btnClear = el('bni-clear');
    var btnDiag  = el('bni-diag');
    if (!input || !send) return;

    // render saved messages
    renderHistory();

    send.addEventListener('click', async function(){
      var msg = (input.value || '').trim();
      if (!msg) return;

      // shortcut: __diag client-side
      if (msg === '__diag') {
        addMsg('__diag', 'user');
        input.value = '';
        setBusy(true);
        try { await sendMessage('__diag'); } finally { setBusy(false); }
        return;
      }

      addMsg(msg, 'user');
      input.value = '';
      setBusy(true);
      try { await sendMessage(msg); } finally { setBusy(false); }
    });

    input.addEventListener('keydown', function(e){
      if (e.key === 'Enter') send.click();
      if (e.key === 'Enter' && e.ctrlKey) input.value = '__diag';
    });

    if (btnClear) {
      btnClear.addEventListener('click', function(){
        if (confirm('Clear chat history?')) clearHistoryUI();
      });
    }

    if (btnDiag) {
      btnDiag.addEventListener('click', async function(){
        addMsg('__diag', 'user');
        setBusy(true);
        try { await sendMessage('__diag'); } finally { setBusy(false); }
      });
    }
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') boot();
  else document.addEventListener('DOMContentLoaded', boot);
})();
</script>
        <?php
        return ob_get_clean();
    }

    /* ---------- REST: /wp-json/bni/v1/chat ---------- */
    public function register_rest() {
        register_rest_route('bni/v1', '/chat', [
            'methods'  => 'POST',
            'callback' => [$this, 'handle_chat'],
            'permission_callback' => function( $request ) {
                // 🔐 Flexible permission:
                // 1) Try REST nonce (works for logged-in); 2) Otherwise allow same-origin referrer; 3) Fallback true for public same-site usage
                $nonce = $request->get_header('X-WP-Nonce');
                if ( $nonce && wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) return true;
                $ref = $request->get_header('referer');
                if ( $ref ) {
                    $site = get_site_url();
                    if ( strpos($ref, $site) === 0 ) return true;
                }
                return true; // প্রোডাকশনে আরও টাইট করতে চাইলে এখানে কাস্টম ভ্যালিডেশন দিন
            }
        ]);
    }

    public function handle_chat( WP_REST_Request $req ) {
        try {
            $message = trim( (string) $req->get_param('message') );
            if ($message === '') {
                return new WP_REST_Response(['error' => 'Empty message'], 400);
            }

            $opts   = get_option(self::OPT_NAME, []);
            $base   = rtrim($opts['base_url'] ?? 'https://generativelanguage.googleapis.com/v1', '/');
            $apiKey = $opts['api_key'] ?? '';
            $model  = $opts['model'] ?? 'gemini-1.5-flash-latest';
            $system = $opts['system'] ?? 'You are a helpful assistant.';

            if (empty($apiKey)) {
                return new WP_REST_Response(['error' => 'Gemini not configured'], 500);
            }

            // (NEW) client history গ্রহণ
            $history = $req->get_param('history');
            if (!is_array($history)) $history = [];

            /* Health check: "__diag" → GET /v1/models?key=...   (200=OK, 401=bad key) */
            if ($message === '__diag') {
                $diag_url = $base . '/models?key=' . rawurlencode($apiKey);
                $diag = wp_remote_get($diag_url, [
                    'headers' => [ 'Accept' => 'application/json' ],
                    'timeout' => 20,
                ]);

                if ( is_wp_error($diag) ) {
                    return new WP_REST_Response([
                        'reply' => 'Diag: WP_Error (outbound/SSL blocked)',
                        'raw'   => $diag->get_error_message(),
                    ], 200);
                }

                $dcode = wp_remote_retrieve_response_code($diag);
                $dbody = wp_remote_retrieve_body($diag);
                return new WP_REST_Response([
                    'reply' => 'Diag: /models status ' . $dcode,
                    'raw'   => substr($dbody ?? '', 0, 400),
                ], 200);
            }

            /* Build contents from history + system + current message */
            $contents = [];

            // system নির্দেশনা প্রথমে (Gemini v1 এ আলাদা system role নেই)
            $contents[] = [
                'role'  => 'user',
                'parts' => [[ 'text' => "System: {$system}" ]],
            ];

            // আগের টার্নগুলো (user/model)
            foreach ($history as $turn) {
                $who = isset($turn['who']) ? $turn['who'] : '';
                $txt = isset($turn['text']) ? $turn['text'] : '';
                $txt = is_string($txt) ? wp_strip_all_tags( wp_specialchars_decode($txt) ) : '';
                if ($txt === '') continue;

                $role = ($who === 'user') ? 'user' : 'model';
                $contents[] = [ 'role' => $role, 'parts' => [[ 'text' => $txt ]] ];
            }

            // বর্তমান মেসেজ
            $contents[] = [ 'role' => 'user', 'parts' => [[ 'text' => $message ]] ];

            /* Gemini generateContent: POST {base}/models/{model}:generateContent?key=API_KEY */
            $endpoint = sprintf(
                '%s/models/%s:generateContent?key=%s',
                $base,
                rawurlencode($model),
                rawurlencode($apiKey)
            );

            $body_arr = [ 'contents' => $contents ];

            $resp = wp_remote_post($endpoint, [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'body'    => wp_json_encode($body_arr),
                'timeout' => 25,
            ]);

            if ( is_wp_error($resp) ) {
                error_log('[GEMINI] WP_Error: ' . $resp->get_error_message());
                return new WP_REST_Response(['error' => 'WP HTTP error', 'detail' => $resp->get_error_message()], 500);
            }

            $code = wp_remote_retrieve_response_code($resp);
            $body = wp_remote_retrieve_body($resp);
            $json = json_decode($body, true);

            /* ---- 404 হলে -latest অটো-ফলব্যাক একবার ট্রাই ---- */
            if ($code === 404 && strpos($body ?? '', 'NOT_FOUND') !== false && strpos($model, '-latest') === false) {
                $tryModel = $model . '-latest';
                $endpoint2 = sprintf('%s/models/%s:generateContent?key=%s', $base, rawurlencode($tryModel), rawurlencode($apiKey));
                $resp2 = wp_remote_post($endpoint2, [
                    'headers' => [
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'body'    => wp_json_encode($body_arr),
                    'timeout' => 25,
                ]);
                if (!is_wp_error($resp2)) {
                    $code = wp_remote_retrieve_response_code($resp2);
                    $body = wp_remote_retrieve_body($resp2);
                    $json = json_decode($body, true);
                }
            }

            if ($code >= 200 && $code < 300 && is_array($json)) {
                // ✅ নানা শেপ কভার করি
                $reply = null;
                if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = $json['candidates'][0]['content']['parts'][0]['text'];
                } elseif (isset($json['candidates'][0]['content'][0]['text'])) {
                    $reply = $json['candidates'][0]['content'][0]['text'];
                } elseif (isset($json['candidates'][0]['content']['parts']) && is_array($json['candidates'][0]['content']['parts'])) {
                    $parts = array_map(function($p){ return is_string($p['text'] ?? null) ? $p['text'] : ''; }, $json['candidates'][0]['content']['parts']);
                    $reply = trim(implode("\n", array_filter($parts)));
                }
                if (!$reply) $reply = 'No reply field in response.';
                return ['reply' => $reply, 'raw' => $json];
            }

            // 400/401/403/429/5xx: বিস্তারিত UI-তে পাঠাই
            error_log('[GEMINI] error code=' . $code . ' body=' . substr($body ?? '', 0, 800));
            return new WP_REST_Response([
                'error'  => 'Gemini API error',
                'status' => $code,
                'body'   => $body,
            ], 500);

        } catch (Throwable $e) {
            error_log('[GEMINI] Fatal: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            return new WP_REST_Response(['error' => 'Server exception', 'detail' => $e->getMessage()], 500);
        }
    }

    /* ---------- Settings Page ---------- */
    public function settings_menu() {
        add_options_page(
            'BNI Chatbot (Gemini)',
            'BNI Chatbot (Gemini)',
            'manage_options',
            'bni-chatbot-gemini',
            [$this, 'settings_page_html']
        );
    }

    public function register_settings() {
        register_setting(self::OPT_GROUP, self::OPT_NAME, [
            'type' => 'array',
            'sanitize_callback' => function($opts){
                return [
                    'base_url' => isset($opts['base_url']) ? esc_url_raw($opts['base_url']) : 'https://generativelanguage.googleapis.com/v1',
                    'api_key'  => isset($opts['api_key'])  ? sanitize_text_field($opts['api_key']) : '',
                    'model'    => isset($opts['model'])    ? sanitize_text_field($opts['model']) : 'gemini-1.5-flash-latest',
                    'system'   => isset($opts['system'])   ? sanitize_text_field($opts['system']) : 'You are a helpful assistant.',
                ];
            },
            'default' => [
                'base_url' => 'https://generativelanguage.googleapis.com/v1',
                'api_key'  => '',
                'model'    => 'gemini-1.5-flash-latest',
                'system'   => 'You are a helpful assistant.',
            ],
        ]);
    }

    public function settings_page_html() { 
        $opts = get_option(self::OPT_NAME, []);
        ?>
        <div class="wrap">
            <h1>BNI Chatbot (Gemini)</h1>
            <form method="post" action="options.php">
                <?php settings_fields(self::OPT_GROUP); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Base URL</label></th>
                            <td>
                                <input type="url" style="width:420px" name="<?php echo esc_attr(self::OPT_NAME); ?>[base_url]"
                                    value="<?php echo esc_attr($opts['base_url'] ?? 'https://generativelanguage.googleapis.com/v1'); ?>"
                                    placeholder="https://generativelanguage.googleapis.com/v1" />
                                <p class="description"></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>API Key</label></th>
                            <td>
                                <input type="text" style="width:420px" name="<?php echo esc_attr(self::OPT_NAME); ?>[api_key]"
                                    value="<?php echo esc_attr($opts['api_key'] ?? ''); ?>"
                                    placeholder="AIza..." />
                                <p class="description">Google API key</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Model</label></th>
                            <td>
                                <input type="text" style="width:420px" name="<?php echo esc_attr(self::OPT_NAME); ?>[model]"
                                    value="<?php echo esc_attr($opts['model'] ?? 'gemini-2.5-flash'); ?>"
                                    placeholder="gemini-2.5-flash" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new BNI_Chatbot_Gemini_SingleFile_V1();
