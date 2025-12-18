H5P Experience Extractor (local_h5p_xapi)
======================================

Overview
--------
H5P Experience Extractor is a local Moodle plugin that captures xAPI statements emitted exclusively by H5P content and forwards them to a specified Learning Record Store (LRS). 
To improve reliability, statements can be stored locally and resent later if delivery to the LRS fails.

The H5P Experience Extractor plugin was designed to complement Logstore xAPI plugin https://moodle.org/plugins/logstore_xapi for a thorough coverage of Learning Analytics Data.

This plugin was developed by members of the Hector Research Institute of Education Sciences and Psychology and is openly distributed; please acknowledge the authors in any publications that use it.

Key features
------------
- Listens for xAPI events from embedded H5P content (Both Moodle or Iframe).
- Sends statements to a configurable LRS endpoint.
- Stores failed statements in a Moodle DB table for later retry.
- Admin report page with:
  - List of stored statements
  - Individual resend and remove actions
  - Batch resend and "clear all" actions

Requirements
------------
- Moodle: tested on Moodle 4.5
- A Learning Record Store, for example https://www.sqllrs.com/ 
  It is important that the LRS endpoint for recieving statements follows the convention of ending in ...xapi/statements
- For complete user activity coverage: https://moodle.org/plugins/logstore_xapi

Installation
------------
1. Download the plugin as a zip archive 
2. Visit Site administration → Install Plugins -> Install Plugins -> Install plugin from ZIP file
3. Configure LRS settings either after the installation or in Site administration → Plugins → local plugins → H5P xAPI (or via the plugin settings page).

Configuration
-------------
Plugin settings (admin UI):
- lrs_endpoint — The base URL of your LRS (e.g. https://lrs.example.org/lrs).
- lrs_username — LRS credentials key.
- lrs_password — LRS credentials password.
- id_schema — Option to choose how actor IDs are mapped (username vs email). Set with the plugin settings.

Accessing the report page
-------------------------
From Moodle Plugin menu:
Admin -> Plugins -> Local -> H5P Experience Report
Report page URL:
- /local/h5p_xapi/report.php

Permissions:
- By default the report page requires the capability moodle/site:config (administrator).

Usage notes
-----------
- If the LRS is unreachable or returns an error, the handler stores the statement in the Moodle database and returns an appropriate HTTP status (502/503 depending on the error).
- Use the report page to review stored statements and resend them. Each resend shows the HTTP response code and response body returned by the LRS.

License & credits
-----------------
- GPL v3 or later (same as Moodle).
- Copyright 2025 Alla Kutkina, Dr. Björn Rudzewitz,
  Hector Research Institute of Education Sciences and Psychology

Please cite
-----------
If you use this plugin or its data in a publication, please acknowledge it.

APA:
Kutkina, A., & Rudzewitz, B. (2025). H5P Experience Extractor (local_h5p_xapi) [Moodle plugin]. Hector Research Institute of Education Sciences and Psychology. https://github.com/allakutkina/h5p_xapi

BibTeX:
@misc{kutkina2025h5p,
  author = {Kutkina, Alla and Rudzewitz, Björn},
  title = {H5P Experience Extractor (local_h5p_xapi)},
  year = {2025},
  howpublished = {Moodle plugin},
  note = {Hector Research Institute of Education Sciences and Psychology},
  url = {https://github.com/allakutkina/h5p_xapi}
}


Contact
-----------------------
- Please open new issues in this repository if you want to report bugs or suggest improvements. 
  Include reproducible steps and any relevant logs when reporting bugs.
