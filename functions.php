# Add A Discount If Total > amount
add_filter( 'woocommerce_calculated_total', 'custom_cart_grand_total', 20, 2 );
function custom_cart_grand_total( $total, $cart ) {
	if (!(IsUserMember())) {
		if ($total > 4000) return $total * 0.9;
	}
	return $total;
}
/*
function get_current_user_id() {
    if ( ! function_exists( 'wp_get_current_user' ) ) {
        return 0;
    }
    $user = wp_get_current_user();
    return ( isset( $user->ID ) ? (int) $user->ID : 0 );
}
*/

function IsUserMember() {
	$user_id = get_current_user_id();
	$plans = array(3865,3864,3863);
	foreach ($plans as $plan) {
		if (wc_memberships_is_user_member( $user_id, $plan ))
			return true;
	}
	return false;
}

add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order');
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

# MemberShip
function custom_woocommerce_auto_complete_order( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
	
  
  	$user_id = $order->get_user_id();
	
	$order_total = $order->get_total();
	$totalSpent = wc_get_customer_total_spent($user_id);
	$totalSpent = max($totalSpent, $order_total);
	if ($totalSpent >= 20000) {
		add_plan($user_id,3865);
	} elseif ($totalSpent >= 10000) {
		add_plan($user_id,3864);
	} elseif ($totalSpent >= 4000) {
		add_plan($user_id,3863);
	}
}
