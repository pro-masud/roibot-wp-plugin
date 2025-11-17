
<?php
$roibot_opts  = isset($options) ? $options : get_option('roibot_settings', array());
$default1_url = ROIBOT_PLUGIN_URL . 'public/images/user-1.jpeg';
$default2_url = ROIBOT_PLUGIN_URL . 'public/images/user-2.jpg';

$avatar1_url = ! empty($roibot_opts['avatar1']) ? esc_url($roibot_opts['avatar1']) : $default1_url;
$avatar2_url = ! empty($roibot_opts['avatar2']) ? esc_url($roibot_opts['avatar2']) : $default2_url;
?>

<div class="chatbot-wrap">
<div class="help-text">
    <div class="close-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </div>
    <span class="material-symbols-outlined"><svg width="40" height="40" viewBox="0 0 57 61" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M55.994 32.418C55.943 24.647 51.236 17.969 44.527 15.027L48.233 6.889C48.479 6.954 48.732 7 48.998 7C50.652 7 51.998 5.654 51.998 4C51.998 2.346 50.652 1 48.998 1C47.344 1 45.998 2.346 45.998 4C45.998 5.032 46.522 5.944 47.319 6.484L43.596 14.66C41.502 13.87 39.243 13.418 36.876 13.418H20.126C17.758 13.418 15.498 13.871 13.403 14.661L9.68 6.484C10.476 5.944 11.001 5.032 11.001 4C11.001 2.346 9.655 1 8.001 1C6.347 1 5.001 2.346 5.001 4C5.001 5.654 6.347 7 8.001 7C8.267 7 8.52 6.954 8.766 6.889L12.472 15.028C5.765 17.971 1.058 24.648 1.008 32.418H1V32.543V37.293V37.418H1.006C1.074 47.906 9.621 56.418 20.125 56.418H36.875C47.379 56.418 55.925 47.906 55.994 37.418H56V37.293V32.543V32.418H55.994ZM48.998 2C50.101 2 50.998 2.897 50.998 4C50.998 5.103 50.101 6 48.998 6C47.895 6 46.998 5.103 46.998 4C46.998 2.897 47.896 2 48.998 2ZM6 4C6 2.897 6.897 2 8 2C9.103 2 10 2.897 10 4C10 5.103 9.103 6 8 6C6.897 6 6 5.103 6 4ZM36.875 55.418H20.125C10.131 55.418 2 47.287 2 37.293V32.543C2 22.549 10.131 14.418 20.125 14.418H36.875C46.869 14.418 55 22.549 55 32.543V37.293C55 47.287 46.869 55.418 36.875 55.418Z" fill="white" stroke="white"/> <path d="M39.5 59.418H18.5C18.224 59.418 18 59.642 18 59.918C18 60.194 18.224 60.418 18.5 60.418H39.5C39.776 60.418 40 60.194 40 59.918C40 59.642 39.776 59.418 39.5 59.418Z" fill="white" stroke="white"/> <path d="M35.295 22.418H20.705C14.251 22.418 9 27.669 9 34.123V34.713C9 41.167 14.251 46.418 20.705 46.418H33.029C33.115 47.198 33.451 48.71 34.743 49.85C36.373 51.289 38.598 51.419 38.693 51.424C38.701 51.425 38.71 51.425 38.718 51.425C38.886 51.425 39.043 51.34 39.136 51.198C39.234 51.049 39.244 50.859 39.164 50.7C39.16 50.692 38.766 49.888 38.667 48.551C38.59 47.52 38.637 46.465 38.67 45.94C43.592 44.485 46.999 39.973 46.999 34.844V34.123C47 27.669 41.749 22.418 35.295 22.418ZM46 34.844C46 39.638 42.739 43.842 38.071 45.07C37.868 45.124 37.719 45.299 37.7 45.508C37.694 45.571 37.556 47.095 37.67 48.626C37.722 49.317 37.843 49.887 37.963 50.309C37.265 50.159 36.227 49.827 35.405 49.101C33.981 47.845 34 45.949 34 45.93C34.002 45.796 33.951 45.665 33.856 45.57C33.762 45.474 33.633 45.42 33.5 45.42H20.705C14.802 45.42 10 40.617 10 34.715V34.125C10 28.222 14.802 23.42 20.705 23.42H35.295C41.197 23.42 46 28.222 46 34.125V34.844Z" fill="white" stroke="white"/> <path d="M18 31.418C16.346 31.418 15 32.764 15 34.418C15 36.072 16.346 37.418 18 37.418C19.654 37.418 21 36.072 21 34.418C21 32.764 19.654 31.418 18 31.418ZM18 36.418C16.897 36.418 16 35.521 16 34.418C16 33.315 16.897 32.418 18 32.418C19.103 32.418 20 33.315 20 34.418C20 35.521 19.103 36.418 18 36.418Z" fill="white" stroke="white"/> <path d="M28 31.418C26.346 31.418 25 32.764 25 34.418C25 36.072 26.346 37.418 28 37.418C29.654 37.418 31 36.072 31 34.418C31 32.764 29.654 31.418 28 31.418ZM28 36.418C26.897 36.418 26 35.521 26 34.418C26 33.315 26.897 32.418 28 32.418C29.103 32.418 30 33.315 30 34.418C30 35.521 29.103 36.418 28 36.418Z" fill="white" stroke="white"/> <path d="M38 31.418C36.346 31.418 35 32.764 35 34.418C35 36.072 36.346 37.418 38 37.418C39.654 37.418 41 36.072 41 34.418C41 32.764 39.654 31.418 38 31.418ZM38 36.418C36.897 36.418 36 35.521 36 34.418C36 33.315 36.897 32.418 38 32.418C39.103 32.418 40 33.315 40 34.418C40 35.521 39.103 36.418 38 36.418Z" fill="white" stroke="white"/> <path d="M9.88341 23.916C10.0354 23.916 10.1844 23.847 10.2834 23.717C11.6214 21.942 13.5424 20.671 15.6914 20.137C15.9594 20.071 16.1224 19.799 16.0564 19.531C15.9894 19.263 15.7184 19.102 15.4504 19.166C13.0794 19.755 10.9614 21.157 9.48441 23.114C9.31841 23.335 9.36241 23.648 9.58241 23.815C9.67341 23.883 9.77941 23.916 9.88341 23.916Z" fill="white" stroke="white"/> </svg></span>
    <h3>👋 <?php _e('Do you have any Questions? we would love to help', 'hello-elementor-child');?> </h3>
</div>
<button class="chatbot-toggler">
    <div class="landing_page_icon">
        <svg width="36" height="27" viewBox="0 0 336 270" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M82.2688 196.882C67.1585 196.882 54.9351 184.521 54.9351 169.256V120H19.195C9.07753 120 0.855469 128.288 0.855469 138.541V227.509C0.855469 237.735 9.05998 246.041 19.195 246.041H28.5139V258.499C28.5139 268.415 38.5787 273.666 44.9141 267.061L65.0437 246.067H161.445C171.562 246.067 179.784 237.78 179.784 227.535V196.944H82.2864L82.26 196.891L82.2688 196.882Z" fill="white"></path>
            <path d="M307.913 0L96.6527 27.1425C81.6012 27.1425 69.4258 39.2666 69.4258 54.2497V152.937C69.4258 167.92 81.6012 180.044 96.6527 180.044H225.623L274.616 225.32C280.924 231.823 290.956 226.652 290.956 216.919V180.053H307.913C322.965 180.053 335.14 167.929 335.14 152.946V27.1072C335.14 12.1241 322.965 0 307.913 0ZM133.735 122.812C120.836 122.812 110.39 112.408 110.39 99.5695C110.39 86.7307 120.836 76.3272 133.735 76.3272C146.634 76.3272 157.08 86.7307 157.08 99.5695C157.08 112.408 146.634 122.812 133.735 122.812ZM202.279 122.812C189.38 122.812 178.934 112.408 178.934 99.5695C178.934 86.7307 189.38 76.3272 202.279 76.3272C215.177 76.3272 225.623 86.7307 225.623 99.5695C225.623 112.408 215.177 122.812 202.279 122.812ZM270.822 122.812C257.924 122.812 247.477 112.408 247.477 99.5695C247.477 86.7307 257.924 76.3272 270.822 76.3272C283.721 76.3272 294.167 86.7307 294.167 99.5695C294.167 112.408 283.721 122.812 270.822 122.812Z" fill="#7AC25A"></path>
        </svg>
    </div>
</button>
</div>

<div class="hidden show-chatbot">
    <div class="chatbot">
        <div class="chatbot-header">
           <h2><?php echo esc_html( $roibot_opts['header_name'] ?? 'ROI' ); ?></h2>
            <div class="avtar">
                <img src="<?php echo $avatar1_url; ?>" alt="">
                <img src="<?php echo $avatar2_url; ?>" alt="">
            </div>
            <p><?php echo esc_html( $roibot_opts['popup_text'] ?? 'We are here to take the weight off your shoulders. Tell us about your challenges.' ); ?></p>
            <span class="material-symbols-outlined close-btn"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M4.28 3.22a.75.75 0 0 0-1.06 1.06L6.94 8l-3.72 3.72a.75.75 0 1 0 1.06 1.06L8 9.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L9.06 8l3.72-3.72a.75.75 0 0 0-1.06-1.06L8 6.94z" clip-rule="evenodd"/></svg></span>
        </div>


        <div class="chat-option">
            <ul class="chatlist" id="chat-list">
                   <?php if ( function_exists('do_shortcode') && shortcode_exists('bni_chatbot') ) : ?>
                        <?php echo do_shortcode('[bni_chatbot]'); ?>
                    <?php endif; ?>
                </ul>
            <ul class="hidden news-list" id="news-list"></ul>
        </div>

        <div class="chatbot-footer">
            <ul>
                <li class="single-chat active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3 20.077V4.616q0-.691.463-1.153T4.615 3h14.77q.69 0 1.152.463T21 4.616v10.769q0 .69-.463 1.153T19.385 17H6.077zM6.5 13.5h7v-1h-7zm0-3h11v-1h-11zm0-3h11v-1h-11z"/></svg>
                    <span>Chat</span>
                </li>
                <li class="news">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="#1A1A1A" stroke-linecap="round" stroke-width="1.7" d="M20 4a6.98 6.98 0 0 1 2.101 5c0 1.959-.804 3.73-2.101 5" class="7acdb305stroke"></path><mask id="c156a" fill="#fff"><path fill-rule="evenodd" d="M9.5 14h.718l5.231 3.452A1 1 0 0 0 17 16.617V1.858a1 1 0 0 0-1.55-.835L10.937 4H5a5 5 0 0 0-1.923 9.617l1.967 7.212a2.268 2.268 0 0 0 4.456-.597z" clip-rule="evenodd"></path></mask><path fill="#1A1A1A" d="m10.218 14 .936-1.419-.426-.28h-.51zM9.5 14v-1.7H7.8V14zm5.95 3.452-.937 1.42zm0-16.429-.937-1.419zM10.937 4v1.7h.51l.426-.28zm-7.86 9.617 1.64-.447-.22-.802-.766-.32zm1.966 7.212 1.64-.447zm5.174-8.529H9.5v3.4h.718zm6.168 3.733-5.232-3.452L9.28 15.42l5.232 3.452zm-1.086.584a.7.7 0 0 1 1.085-.584l-1.872 2.838c1.795 1.185 4.187-.103 4.187-2.253zm0-14.759v14.76h3.4V1.858zm1.085.584a.7.7 0 0 1-1.085-.584h3.4c0-2.15-2.392-3.438-4.187-2.254zm-4.51 2.977 4.51-2.977-1.872-2.838-4.511 2.977zM5 5.7h5.938V2.3H5zM1.7 9A3.3 3.3 0 0 1 5 5.7V2.3c-3.7 0-6.7 3-6.7 6.7zm2.032 3.048A3.3 3.3 0 0 1 1.7 9h-3.4a6.7 6.7 0 0 0 4.123 6.186zm2.952 8.334L4.718 13.17l-3.28.895 1.966 7.211zm.548.418a.57.57 0 0 1-.548-.418l-3.28.894A3.97 3.97 0 0 0 7.232 24.2zm.568-.568a.57.57 0 0 1-.568.568v3.4a3.97 3.97 0 0 0 3.968-3.968zM7.8 14v6.232h3.4V14z" class="cef0abbbfill" mask="url(#c156a)"></path></svg>
                    <span>News</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        // Existing script for more-options toggle
        $(".more-options").click(function () {
            $(this).toggleClass("toggle");
        });

        // New script for closing the help-text when chatbot-toggler is clicked
        $(".chatbot-toggler").click(function () {
            $(".help-text").hide(); // Hides the help-text element
        });
    });

    // jQuery(document).ready(function ($) {
    //     $(".chatbot-toggler").click(function () {
    //         $('html').addClass("open-chatbot");
    //     });
    //     $(".close-btn").click(function () {
    //         $("html").removeClass("open-chatbot");
    //     });
    // });

    jQuery(document).ready(function ($) {
        // OLD:
        // $(".chatbot-toggler").click(function () { $('html').addClass("open-chatbot"); });
        // $(".close-btn").click(function () { $("html").removeClass("open-chatbot"); });

        // NEW: no html overlay class at all; just show/hide the widget
        $(".chatbot-toggler").on("click", function () {
            $(".show-chatbot").removeClass("hidden");
        });
        $(".close-btn").on("click", function () {
            $(".show-chatbot").addClass("hidden");
        });
    });

</script>
