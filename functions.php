function so_37863005_checkout_url( $url ){
	$scheme = ( is_ssl() || 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ) ? 'https' : 'http';
	$url = site_url( '/cart/', $scheme );

	return $url;
}
add_filter( 'woocommerce_get_checkout_url', 'so_37863005_checkout_url', 10, 2);

add_action('woocommerce_order_status_changed', 'ts_auto_complete_by_payment_method');
function add_plan($user_id, $plan) {
	if (!wc_memberships_is_user_member( $user_id, $plan )) {
		$args = array(
			// Enter the ID (post ID) of the plan to grant at registration
			'plan_id'   => $plan,
			'user_id'   => $user_id,
		);
		wc_memberships_create_user_membership( $args );
	}
}
function ts_auto_complete_by_payment_method($order_id)
{
  
  if ( ! $order_id ) {
        return;
  }
  global $product;
  $order = wc_get_order( $order_id );
  
  $user_id = $order->get_user_id();
	
	$order_total = $order->get_total();
	$totalSpent = wc_get_customer_total_spent($user_id);
	$totalSpent = max($totalSpent, $order_total);
	//file_put_contents('/var/www/html/log/log.txt',$totalSpent);
	if ($totalSpent >= 20000) {
		add_plan($user_id,3865);
	} elseif ($totalSpent >= 10000) {
		add_plan($user_id,3864);
	} elseif ($totalSpent >= 4000) {
		add_plan($user_id,3863);
	}
	
	/*if ($order->data['status'] == 'processing') {
        $payment_method=$order->get_payment_method();
        if ($payment_method!="cod")
        {
            $order->update_status( 'completed' );
        }
      
  }*/
  
}

function action_woocommece_order_status_completed($order_id) {
	$order = wc_get_order($order_id);
	$user_id = $order->get_user_id();
	$order_total = $order->get_total();
	$totalSpent = wc_get_customer_total_spent($user_id);
	$user = get_userdata($user_id);
	$totalSpent = max($totalSpent, $order_total);
	if ($totalSpent >= 20000) {
		$user->add_role('diamond');
	} elseif ($totalSpent >= 10000) {
		$user->remove_role('gold');
	} elseif ($totalSpent >= 4000) {
		$user->remove_role('bronze'); 
		$user->add_role('silver');
	}
}

add_action( 'woocommerce_payment_complete','new_email_notification' );
function new_email_notification($order_id) 
{
    $order = wc_get_order($order_id);
	$user_id = $order->get_user_id();
	echo"<script>console.log({$user_id})</script>";
	$order_total = $order->get_total();
	$totalSpent = wc_get_customer_total_spent($user_id);
	$user = get_userdata($user_id);
	$totalSpent = max($totalSpent, $order_total);
	if ($totalSpent >= 20000) {
		$user->add_role('diamond');
	} elseif ($totalSpent >= 10000) {
		$user->remove_role('gold');
	} elseif ($totalSpent >= 4000) {
		$user->remove_role('bronze'); 
		$user->add_role('silver');
	}
}

add_action('woocommerce_order_status_completed','ction_woocommece_order_status_completed',10,1);





add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
add_filter( 'woocommerce_checkout_update_order_review_expired', '__return_false' );

add_action( 'woocommerce_order_status_completed', 'wpglorify_change_role_on_purchase' );



function wpglorify_change_role_on_purchase( $order_id ) {

// get order object and items
	$order = new WC_Order( $order_id );
	
	$user = new WP_User( $order->get_customer_id() );
	$order_total = $order->get_total();
	$totalSpent = wc_get_customer_total_spent($user_id);
	
	$totalSpent = max($totalSpent, $order_total);
	if ($totalSpent >= 20000) {
		$user->add_role('diamond');
	} elseif ($totalSpent >= 10000) {
		$user->remove_role('gold');
	} elseif ($totalSpent >= 4000) {
		$user->remove_role('bronze'); 
		$user->add_role('silver');
	}
}
