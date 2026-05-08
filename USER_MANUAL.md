# Novuflow 2.0 User Guide

## What this document covers

This user guide explains how to use the features currently implemented in the Novuflow 2.0 application. It reflects the actual functionality available in the system today.

## Table of contents

1. [Getting Started](#getting-started)
2. [User Roles](#user-roles)
3. [Navigation Overview](#navigation-overview)
4. [Dashboard](#dashboard)
5. [Projects](#projects)
6. [Kanban Board](#kanban-board)
7. [Tasks](#tasks)
8. [Search](#search)
9. [Comments and Attachments](#comments-and-attachments)
10. [Teams](#teams)
11. [Notifications](#notifications)
12. [Admin Area](#admin-area)
13. [HRIS Links](#hris-links)
14. [Troubleshooting](#troubleshooting)

## Getting Started

### Login and registration

- Use the **Login** page to sign in.
- New users can use the **Register** page to create an account.
- If you forget your password, use **Forgot Password** to receive a reset link.

### Profile

- Access your profile from the dashboard or user menu.
- Update your personal details and save changes.

## User Roles

Novuflow 2.0 has four main role types:

- **SuperAdmin**: Full access to all areas, including admin tools.
- **Admin**: Manage users, teams, and view analytics.
- **Team Leader**: Manage projects and team members for their team.
- **Employee**: Work on assigned tasks and participate in projects you belong to.

### Important role rules

- **Employees** cannot create new projects.
- **Team Leaders** can create projects for their own team.
- **Admins** and **SuperAdmins** can create and edit projects and teams.
- **Employees** can comment on tasks they are assigned to.

## Navigation Overview

The main navigation includes:

- **Dashboard**: Overview of your projects, tasks, and stats.
- **Projects**: View the list of projects you can access.
- **Board / Kanban**: View tasks in columns for a selected project.
- **Team**: View your team or available teams and members.
- **Notifications**: See recent alerts and messages.
- **Search**: Find tasks and projects quickly.
- **HRIS**: clock in / clock out links.

## Dashboard

The dashboard provides a quick summary of your current work.

### What you can do from the dashboard

- See a personalized welcome message.
- View counts of projects and active tasks.
- Access notifications and profile settings.
- Navigate to projects, team, search, and reports.

## Projects

### Project list

- Go to **Projects** to see all projects you can access.
- Use search and status filters if available.
- Click a project to open its details page.

### Creating a project

Who can create projects:

- **Admins** and **SuperAdmins**
- **Team Leaders** for their own team

Steps:

1. Open **Projects**.
2. Click **Create Project**.
3. Enter:
   - Project name
   - Description
   - Status (Active / Completed / On Hold)
   - Start date
   - Due date
   - Team assignment
4. Add project members.
5. Save the project.

### Editing a project

- Open a project and click **Edit**.
- Update name, description, dates, status, or team.
- Save changes.

### Project members and invitations

- Project creators can invite team members.
- Invited users have a **pending** status until they accept.
- Pending members see an invitation page for the project.
- Accepted members can access the project and tasks.

### Project details page

From the project details page you can:

- See project progress and status.
- View project tasks.
- Review project activity and recent updates.
- Add or remove project members.
- Manage project columns.

## Kanban Board

### Access

- Open the board from a project page or the **Board** menu.
- The board shows tasks grouped by column.

### What you can do

- View tasks by status.
- Switch between **all tasks** and **my tasks**.
- Create new tasks (if your role allows it).
- Assign tasks to team members.
- Move tasks through columns by changing status.

### Columns

- Default columns are: **To Do**, **In Progress**, **Review**, **Completed**.
- Project owners and admins can add, rename, delete, and reorder columns.
- If a column is deleted, tasks are moved to another available column.

## Tasks

### Creating tasks

- Use the **Add Task** button on the board or project page.
- Enter:
  - Task title
  - Description
  - Status (matching project columns)
  - Priority (Low / Medium / High)
  - Due date
  - Assigned team members

### Viewing tasks

- Open a task to see its full details.
- View assignees, project, creator, comments, and attachments.

### Editing tasks

- Open the task and click **Edit**.
- Change title, description, status, priority, due date, assignees, or project.
- Save your changes.

### Deleting tasks

- Task owners, admins, and team leaders can delete tasks.
- Deleting a task removes its assignees and comments.

### Task status updates

- Task status can be updated from the task page or board.
- Employees may only update tasks assigned to them.

## Search

### Using search

- Type into the header search box.
- Results are for tasks and projects only.
- Search uses task titles, task descriptions, project names, and project descriptions.

### Suggestions

- Suggestions appear after typing at least two characters.
- Click a suggestion or press **Enter** to search.

### Search results

- Tasks show title, description, project, status, and priority.
- Projects show name, description, team, and progress.

## Comments and Attachments

### Commenting on tasks

- Open a task and go to the comments section.
- Add a new comment or reply to an existing one.
- Only task assignees, team leaders, and admins can comment.

### Editing and deleting comments

- You can edit or delete your own comments.
- Replies are supported for threaded discussions.

### Attachments

- Attach files to comments using the attachment button.
- Supported file types include images and common documents.
- File size is limited to 10MB per file.
- Attachments appear in the comment thread.

## Teams

### Viewing teams

- Go to **Team** to see your team or available teams.
- Team pages show members, team leader, and team projects.

### Team leader actions

- Invite members to the team.
- View member profiles.
- Assign tasks to team members.
- Change member roles (depending on permissions).

### Admin team management

Admins can:

- Create teams
- Edit team name, description, and leader
- Add or remove members
- Delete teams

### Member profiles

- Open a team member profile to see their assigned tasks and team memberships.

## Notifications

### What notifications are used for

- Task assignments
- Project invitations
- Task comments
- Other in-app alerts

### Managing notifications

- Open the notification list from the bell icon.
- Read notifications to mark them as read.
- Use **Mark All as Read** if available.

## Admin Area

Admin and SuperAdmin users can access:

- **User management**: add, edit, delete users
- **Team management**: create, update, delete teams
- **Analytics**: view system and team metrics

## HRIS Links

- Use the **HRIS Time In** link to set your status online.
- Use the **HRIS Clock Out** link to set your status offline.
- These links open the external HRIS system and update your online status in the application.

## Troubleshooting

### If you cannot access a page

- Check that you are logged in.
- Ensure your role has permission for that section.
- Contact your administrator if access is blocked.

### If search does not return results

- Try simpler words.
- Confirm the search text exists in a task or project.
- Use the search suggestions to select existing items.

### If a comment or attachment fails

- Check file size (10MB maximum).
- Confirm you have permission to comment on the task.
- Refresh the page and try again.

## Notes

- This guide reflects only the features currently available in Novuflow 2.0.
- If you need a feature that is not available, contact your administrator.
