# MCQS Developer

A professional WordPress plugin to manage Multiple Choice Questions, Exams, Analytics, and User Attempts.

## Features

- MCQ question bank with categories
- Exam/quiz creation and management
- User dashboard with quiz attempt history and profile
- Admin analytics for questions, categories, and exams
- Import/export tools (stub, can be extended)
- Two widgets (stats, recent)
- Clean modular code and easy extensibility

## Installation

1. **Upload the plugin directory** (`mcqs-developer`) to your WordPress plugins folder (`/wp-content/plugins/`).
2. Or, zip the `mcqs-developer` folder and upload via the WordPress admin "Plugins > Add New > Upload Plugin" page.
3. Activate the plugin in the WordPress dashboard.

## Usage

- Use the **MCQS Developer** admin menu for questions, categories, exams, analytics, and import/export.
- Add MCQs, assign them to categories, and group them into exams.
- Use the `[mcqs_user_dashboard]` shortcode on any page to display the user dashboard (attempt history/profile).
- Add widgets from Appearance > Widgets for stats or recent MCQs/exams.
- Extend the plugin by editing files in `/includes`.

## Shortcodes

- `[mcqs_user_dashboard]` — Displays the logged-in user's quiz/exam attempt history and profile.

## Developer Notes

- All admin and logic files are in the `/includes` directory.
- To add more features or integrations, create new modules in `/includes` and include them in `mcqs-developer.php`.
- All data is stored in custom database tables prefixed with `mcqs_`.

## Support

Contact [shahidsoomro786@gmail.com](mailto:shahidsoomro786@gmail.com)  
GitHub: [https://github.com/shahidhussainsoomro](https://github.com/shahidhussainsoomro)

---