<?php
/**
 * PusztaPlay Magic Login — REST API
 * QR-kódos TV bejelentkezés végpontjai
 */

if (!defined('ABSPATH')) exit;

add_action('rest_api_init', 'pp_register_rest_routes');

function pp_register_rest_routes() {
  register_rest_route('pusztaplay/v1', '/qr-request', [
    'methods'             => 'POST',
    'callback'            => 'pp_rest_qr_request',
    'permission_callback' => '__return_true',
  ]);

  register_rest_route('pusztaplay/v1', '/qr-poll', [
    'methods'             => 'GET',
    'callback'            => 'pp_rest_qr_poll',
    'permission_callback' => '__return_true',
    'args' => [
      'code' => [
        'required'          => true,
        'sanitize_callback' => 'sanitize_text_field',
      ],
    ],
  ]);
}

function pp_rest_qr_request() {
  $code = strtoupper(wp_generate_password(8, false));

  $data = [
    'status'     => 'pending',
    'created_at' => time(),
  ];
  set_transient('pp_tv_' . $code, $data, 5 * MINUTE_IN_SECONDS);

  $auth_url = add_query_arg('pp_tv', $code, home_url('/'));

  return new WP_REST_Response([
    'code'     => $code,
    'auth_url' => $auth_url,
    'expires_in' => 300,
  ], 200);
}

function pp_rest_qr_poll($request) {
  $code          = $request->get_param('code');
  $transient_key = 'pp_tv_' . $code;
  $data          = get_transient($transient_key);

  if (!$data) {
    return new WP_REST_Response(['status' => 'expired'], 200);
  }

  if ($data['status'] === 'authenticated') {
    $response = [
      'status'      => 'authenticated',
      'xtream_user' => $data['xtream_user'],
      'xtream_pass' => $data['xtream_pass'],
      'package'     => $data['package'] ?? '',
      'sub_end'     => $data['sub_end'] ?? 0,
    ];
    delete_transient($transient_key);
    return new WP_REST_Response($response, 200);
  }

  return new WP_REST_Response(['status' => 'pending'], 200);
}
