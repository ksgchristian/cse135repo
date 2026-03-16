# CSE 135 Final Project — Analytics Reporting Platform

## Live Deployment

**Frontend Test Website**
https://test.christianserrato.com

**Analytics Dashboard (Login Required)**
https://test.christianserrato.com/login.php

**GitHub Repository**
https://github.com/kcgchristian/cse135repo

---

# Project Overview

This project implements a **server-side analytics platform** that collects user interaction data from a website and generates reports that allow administrators and analysts to understand user behavior.

The system simulates how real-world analytics systems operate by collecting user events from a website and transforming them into structured reports that can be interpreted by analysts.

The platform includes:

* Event collection from the frontend website
* Authenticated analytics dashboard
* Role-based access control
* Multiple analytics reports
* Charts and data tables
* Report exporting
* User management functionality

The goal of the project is to demonstrate how a **server-side backend can collect behavioral data and transform it into actionable insights through a reporting interface**.

---

# System Architecture

The application follows a simplified **MVC-style architecture**.

## Model

User activity data is collected and stored in a datastore. The system records events such as:

* Page views
* Product page visits
* Session activity
* Navigation behavior

These events are queried and aggregated to generate analytics reports.

## View

The reporting interface presents analytics data through:

* Charts
* Data tables
* Structured analytics reports

These views allow analysts and administrators to quickly interpret user behavior.

## Controller

Server-side PHP scripts control:

* Authentication
* Role-based access permissions
* Database queries
* Analytics report generation
* Exporting functionality

---

# Authentication System

The platform includes **role-based authentication** with three levels of users.

## Super Admin

Capabilities:

* Manage users
* Access all reports
* Modify system permissions
* View all analytics data

## Analyst

Capabilities:

* Access analytics dashboards
* View analytics reports
* Analyze behavioral data

## Viewer

Capabilities:

* View saved reports
* Access read-only report data

This structure models how many analytics platforms separate responsibilities between administrators, analysts, and report consumers.

---

# Analytics Reports

The backend provides multiple analytics reports designed to interpret user behavior.

## Page Efficiency Report

Analyzes how users interact with pages on the website.

Metrics include:

* Page views
* Load events
* Exit counts

**Purpose:**
Helps identify pages where users engage the most and pages where users frequently leave.

---

## Product Engagement Report

Tracks interaction with product pages.

Metrics include:

* Product page visits
* Engagement frequency
* Interaction counts

**Purpose:**
Helps determine which products generate the most user interest.

---

## Session Activity Report

Analyzes user behavior across browsing sessions.

Metrics include:

* Events per session
* Number of unique pages visited
* Load events and exit behavior

**Purpose:**
Identifies highly engaged sessions and browsing patterns across the site.

---

# Report Export

Reports can be exported for external use.

This allows analysts to:

* Share reports
* Archive analytics data
* Generate documentation for presentations

---

# Design Considerations

The dashboard interface was designed to present analytics information clearly through:

* Visual charts for summaries
* Tables for detailed analytics metrics
* Structured reports to help analysts interpret user behavior

---

# AI Usage Acknowledgement

Generative AI tools were used during development to assist with:

* Debugging server-side code
* Brainstorming analytics report ideas
* Improving documentation clarity
* Suggesting implementation approaches for reporting features

All code was reviewed and integrated manually to ensure it functioned correctly within the project architecture.

AI tools were used as development assistance rather than direct code generation.

---

# Future Improvements

With additional development time, the system could be expanded with:

* Real-time analytics updates
* More advanced visualization tools
* Additional report categories
* Improved UI/UX design
* Automated scheduled reports
* More scalable data storage architecture

---

# Summary

This project demonstrates how a server-side system can collect user activity data and transform it into structured analytics reports through an authenticated dashboard interface.

The implementation highlights key web application concepts including authentication, role-based access control, analytics processing, and data visualization.
