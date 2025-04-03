# Bypass Drip Content for LearnDash

A WordPress plugin that provides granular control over LearnDash's drip-feed content functionality by allowing administrators to bypass drip scheduling for specific users and groups on a per-lesson basis.

![Bypass Drip Content for LearnDash](https://obijuan.dev/wp-content/uploads/2025/04/Screenshot-from-2025-04-02-20-34-45.png)

## Features

- **User-Level Bypass**: Select specific WordPress users to bypass drip-feed restrictions for individual lessons
- **Group-Level Bypass**: Apply bypass settings to entire LearnDash groups
- **Per-Lesson Control**: Configure bypass settings independently for each lesson
- **Smart Search**: Advanced user and group search functionality with real-time AJAX-powered results
- **Intuitive Interface**: Seamless integration with LearnDash's native lesson settings panel
- **Efficient Performance**: Optimized database queries and caching for minimal impact on site performance

## Requirements

- WordPress 5.0 or higher
- LearnDash LMS 3.0 or higher
- PHP 7.2 or higher

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel → Plugins → Add New
3. Click "Upload Plugin" and select the downloaded zip file
4. Click "Install Now" and then "Activate"

## Usage

### Configuring Bypass Settings

1. Navigate to any lesson in WordPress admin (Posts → Lessons)
2. Locate the "Bypass Drip Content Settings" section in the lesson settings
3. Use the search fields to find and select:
   - Individual users by name or email
   - LearnDash groups by name
4. Save the lesson to apply the bypass settings

### Managing Bypass Access

- Selected users will have immediate access to the lesson content regardless of drip-feed settings
- Users belonging to selected groups inherit bypass access automatically
- Changes take effect immediately after saving
- Removing users or groups will reinstate normal drip-feed restrictions

## Security

- All AJAX requests are nonce-verified
- User capabilities are properly checked
- Input data is sanitized
- XSS prevention measures implemented
- Secure data storage using WordPress post meta

## Technical Details

### Integration Points

- Seamlessly integrates with:
  - LearnDash Settings API
  - WordPress Post Meta API
  - WordPress AJAX API
  - WordPress Users API
  - LearnDash Groups System

### Data Storage

- User bypass list: Stored as JSON in post meta `bypass_drip_content`
- Group bypass list: Stored as JSON in post meta `bypass_drip_content_groups`

### Performance Considerations

- Efficient user/group searches with pagination (10 items per page)
- Delayed AJAX searches (250ms) to prevent excessive server requests
- Optimized database queries
- Maintains state to minimize unnecessary reloads

## Support

For bug reports or feature requests, please use the plugin's GitHub repository issue tracker or contact us
at [support@obijuan.dev](mailto:support@obijuan.dev)

## License

This plugin is licensed under the GPL v2 or later.

---
Last updated: April 2, 2025
