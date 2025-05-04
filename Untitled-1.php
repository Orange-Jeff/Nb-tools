🔹 STEP 1: Set a Cookie on Login Based on User Meta or Role
This snippet can go into the Code Snippets plugin or your child theme’s functions.php. It sets a cookie based on a user meta field (e.g., user_type):

php
Copy
Edit
add_action('wp_login', 'set_user_type_cookie', 10, 2);
function set_user_type_cookie($user_login, $user) {
$user_type = get_user_meta($user->ID, 'user_type', true); // e.g., "agent" or "buyer"

if ($user_type) {
setcookie('user_type', $user_type, time() + 3600 * 24 * 7, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
}
}
🔹 STEP 2: Optional — Clear Cookie on Logout
php
Copy
Edit
add_action('wp_logout', 'clear_user_type_cookie');
function clear_user_type_cookie() {
setcookie('user_type', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
}
🔹 STEP 3: Check Cookie in Templates or Shortcodes
In any of the Real Estate Manager templates you loaded into your child theme, you can now add logic like this:

php
Copy
Edit
if (isset($_COOKIE['user_type']) && $_COOKIE['user_type'] === 'agent') {
// Show agent-specific listings or info
} elseif (isset($_COOKIE['user_type']) && $_COOKIE['user_type'] === 'buyer') {
// Show buyer-specific content
}
Or in a Divi Code Module:

php
Copy
Edit
<?php
if (isset($_COOKIE['user_type']) && $_COOKIE['user_type'] === 'agent') {
    echo '<div class="custom-agent-box">Agent View Content</div>';
}
?>
🔒 Important Notes:
Make sure the user_type meta key actually exists and is consistently set during registration or profile updates.

Cookies won't be instantly available on first login unless the page is refreshed — so either redirect after login or use JS/PHP fallbacks if needed.

Set the cookie to HTTP-only and secure for production.

Would you like a complete working example that includes a custom user meta field setup, plus detection + shortcode rendering based on that?

---
Perfect! Based on that registration form, you're using render_registration_field() to dynamically output the form fields. That means we need to identify a specific select field (likely tied to user_type, user_role, or similar), then hook into the form submission logic and use PHP to assign a user role based on its submitted value.

Assuming the form eventually leads to a user creation function via AJAX or POST, here's what to do:

✅ STEP 1: Find the Field Name
Look at the $agent_fields config (where it’s stored in rem_ob->get_agent_fields()) to find the select field name you want to detect. Let's assume it's called:

html
Copy
Edit
<select name="user_type"> <!-- Options like "agent" or "buyer" --> </select>
✅ STEP 2: Hook Into User Registration & Assign Role Based on That Field
Drop this PHP snippet into the Code Snippets plugin:

php
Copy
Edit
add_action('user_register', 'rem_custom_assign_user_role_on_register', 10, 1);
function rem_custom_assign_user_role_on_register($user_id) {
if (isset($_POST['user_type'])) {
$user_type = sanitize_text_field($_POST['user_type']);

// Save it to user meta
update_user_meta($user_id, 'user_type', $user_type);

// Assign custom role based on field
$user = new WP_User($user_id);

if ($user_type === 'agent') {
$user->set_role('agent'); // Or any custom role slug you've defined
} elseif ($user_type === 'buyer') {
$user->set_role('subscriber'); // Adjust role as needed
}
}
}
✅ STEP 3: (Optional) Auto-Approve User or Send Admin Email
If needed, we can also:

Auto-publish REM profile posts

Send a custom notification to admin

Or trigger a redirect for the new user

If you can confirm the field name used for user type selection (I can help find it from the plugin or settings), I’ll finalize the snippet with exact match conditions.

Would you like a downloadable ready-to-paste version of this PHP snippet?
