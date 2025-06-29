# 📅 Calendar API

**Calendar API** is a simple REST API for a hypothetical calendar application where users can register, create their own calendars, events, and tasks.

---

## 🚀 Features

-   User registration and management
-   Creation of multiple calendars per user
-   Support for events with start/end date and time
-   Tasks with completion status
-   Custom colors and display order for calendars

---

## 👤 User

A user who can have multiple calendars.

| Field       | Type       | Description            |
| ----------- | ---------- | ---------------------- |
| `firstName` | `string`   | First name             |
| `lastName`  | `string`   | Last name              |
| `email`     | `string`   | Email                  |
| `password`  | `string`   | Password (hashed)      |
| `createdAt` | `datetime` | Creation date          |
| `updatedAt` | `datetime` | Last modification date |

---

## 📂 Calendar

A calendar to which a user can attach events and tasks.

| Field         | Type                     | Description                   |
| ------------- | ------------------------ | ----------------------------- |
| `title`       | `string(20)`             | Calendar title                |
| `description` | `string(255)` (optional) | Calendar description          |
| `order`       | `int`                    | Display order in the list     |
| `color`       | `char(6)` (optional)     | Color in HEX (e.g., `FF5733`) |
| `owner`       | `User`                   | Calendar owner                |
| `createdAt`   | `datetime`               | Creation date                 |
| `updatedAt`   | `datetime`               | Last modification date        |

---

## 📆 Event

An event with a start and end date. Typically displayed in the calendar as a time block.

| Field         | Type                | Description            |
| ------------- | ------------------- | ---------------------- |
| `title`       | `string`            | Event title            |
| `description` | `string` (optional) | Event description      |
| `startDate`   | `YYYY-MM-DD`        | Start date             |
| `startTime`   | `HH:MM` (optional)  | Start time             |
| `endDate`     | `YYYY-MM-DD`        | End date               |
| `endTime`     | `HH:MM` (optional)  | End time               |
| `createdAt`   | `datetime`          | Creation date          |
| `updatedAt`   | `datetime`          | Last modification date |

---

## ✅ Task

A task that can be marked as completed. Also has a start and end date and time.

| Field         | Type                | Description                      |
| ------------- | ------------------- | -------------------------------- |
| `title`       | `string`            | Task title                       |
| `description` | `string` (optional) | Task description                 |
| `startDate`   | `YYYY-MM-DD`        | Start date                       |
| `startTime`   | `HH:MM` (optional)  | Start time                       |
| `completed`   | `boolean`           | Completion status (`true/false`) |
| `createdAt`   | `datetime`          | Creation date                    |
| `updatedAt`   | `datetime`          | Last modification date           |

---

## 📎 Notes

-   Calendar colors are stored in HEX format (e.g., `#FF5733`), but **saved in the database without `#`** (`char(6)`).
-   All dates and times are formatted according to ISO: `YYYY-MM-DD`, `HH:MM`.
-   In the future, reminders and recurring events can be implemented.

---
