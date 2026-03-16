# GRADER.md

## Project Access

**Frontend Website**
https://test.christianserrato.com

**Analytics Dashboard (Login Page)**
https://test.christianserrato.com/login.php

---

# Login Credentials

### Super Admin

Username: `superadmin`
Password: `SuperAdmin123!`

Capabilities:

* Manage users
* Access all analytics reports
* Full system access

---

### Analyst

Username: `analyst1`
Password: `Analyst123!`

Capabilities:

* View all analytics dashboards
* Access analytics reports
* Analyze user behavior data

---

### Viewer

Username: `viewer1`
Password: `Viewer123!`

Capabilities:

* View saved reports
* Read-only access to analytics information

---

# Suggested Grading Scenario

To evaluate the platform, the following sequence is recommended.

### Step 1 — Generate Analytics Data

Visit the frontend website and interact with several pages:

1. Open the homepage
2. Navigate to the products page
3. Open several product detail pages
4. Refresh or navigate between pages

This generates activity events that will appear in the analytics reports.

---

### Step 2 — Login to the Analytics Dashboard

Navigate to:

https://test.christianserrato.com/login.php

Login using one of the provided credentials.

---

### Step 3 — View Analytics Reports

After logging in, access the reporting dashboard and review the analytics reports.

The platform includes the following reports:

• **Page Efficiency Report**
Displays page loads and exits to determine which pages retain or lose users.

• **Product Engagement Report**
Tracks which product pages receive the most user interaction.

• **Session Activity Report**
Shows session-level analytics such as number of events per session and browsing behavior.

Each report includes both **charts and tables** to present analytics information visually and numerically.

---

### Step 4 — Test Role Permissions

Different user roles should have different capabilities.

* Super Admin: Full access including user management.
* Analyst: Access to analytics reports.
* Viewer: Read-only access to saved reports.

---

# Known Limitations

The test website intentionally contains unusual product IDs and page structures in order to simulate varied analytics data. Because of this, some product IDs may not correspond to real product names.

Additionally, analytics are generated based on user activity during testing, so report values may vary depending on how the site is interacted with before logging into the dashboard.

---

# Architecture Notes

The backend uses server-side PHP to implement:

* authentication and authorization
* analytics data queries
* report generation
* visualization using charts and tables

The system follows a simplified **Model-View-Controller style structure**, where:

* Models represent collected event data
* Views present analytics reports
* Controllers manage authentication and report generation logic

---

# Areas of Potential Improvement

Given additional development time, improvements could include:

* real-time analytics updates
* additional report types
* improved dashboard UI/UX
* more advanced visualization tools
* stronger data normalization for product information

These areas are acknowledged to demonstrate awareness of current system limitations.
