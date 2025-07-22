=== Bypass Drip Content for LearnDash ===
Contributors: obijuan
Donate link: https://obijuan.dev/
Tags: learndash, drip content, lms, learning management, course access
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides granular control over LearnDash's drip-feed content functionality allowing to bypass drip scheduling.

== Description ==

Bypass Drip Content for LearnDash provides granular control over LearnDash's drip-feed content functionality by allowing administrators to bypass drip scheduling for specific users and groups on a per-lesson basis.

### Features

* **User-Level Bypass**: Select specific WordPress users to bypass drip-feed restrictions for individual lessons
* **Group-Level Bypass**: Apply bypass settings to entire LearnDash groups
* **Per-Lesson Control**: Configure bypass settings independently for each lesson
* **Smart Search**: Advanced user and group search functionality with real-time AJAX-powered results
* **Intuitive Interface**: Seamless integration with LearnDash's native lesson settings panel
* **Efficient Performance**: Optimized database queries and caching for minimal impact on site performance

### Requirements

* WordPress 5.0 or higher
* LearnDash LMS 3.0 or higher
* PHP 7.2 or higher

== Installation ==

1. Download the plugin zip file
2. Go to WordPress admin panel → Plugins → Add New
3. Click "Upload Plugin" and select the downloaded zip file
4. Click "Install Now" and then "Activate"

== Usage ==

= Configuring Bypass Settings =

1. Navigate to any lesson in WordPress admin (Posts → Lessons)
2. Locate the "Bypass Drip Content Settings" section in the lesson settings
3. Use the search fields to find and select:
   - Individual users by name or email
   - LearnDash groups by name
4. Save the lesson to apply the bypass settings

= Managing Bypass Access =

* Selected users will have immediate access to the lesson content regardless of drip-feed settings
* Users belonging to selected groups inherit bypass access automatically
* Changes take effect immediately after saving
* Removing users or groups will reinstate normal drip-feed restrictions

== Security ==

* All AJAX requests are nonce-verified
* User capabilities are properly checked
* Input data is sanitized
* XSS prevention measures implemented
* Secure data storage using WordPress post meta

== Technical Details ==

= Integration Points =

* Seamlessly integrates with:
  * LearnDash Settings API
  * WordPress Post Meta API
  * WordPress AJAX API
  * WordPress Users API
  * LearnDash Groups System

= Data Storage =

* User bypass list: Stored as JSON in post meta `bypass_drip_content`
* Group bypass list: Stored as JSON in post meta `bypass_drip_content_groups`

= Performance Considerations =

* Efficient user/group searches with pagination (10 items per page)
* Delayed AJAX searches (250ms) to prevent excessive server requests
* Optimized database queries
* Maintains state to minimize unnecessary reloads

== Frequently Asked Questions ==

= Does this plugin work with LearnDash 4.0? =

Yes, this plugin is compatible with LearnDash 4.0 and higher.

= Can I apply bypass settings to multiple lessons at once? =

Currently, bypass settings must be configured on a per-lesson basis. Bulk editing functionality may be added in future updates.

= Will this plugin affect my existing LearnDash settings? =

No, this plugin only adds additional functionality and does not modify any existing LearnDash settings.

== Screenshots ==

1. Bypass Drip Content settings panel in the lesson editor
2. User search functionality with real-time results
3. Group selection interface

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Bypass Drip Content for LearnDash.

== Support ==

For bug reports or feature requests, please use the plugin's GitHub repository issue tracker or contact us at [support@obijuan.dev](mailto:support@obijuan.dev).

== License ==

This plugin is licensed under the GPL v2 or later.
