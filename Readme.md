H5P Experience Extractor <i>(local_h5p_xapi)</i>
======================================

Overview
--------
<b>H5P Experience Extractor</b> is a local Moodle plugin that captures xAPI statements emitted exclusively by H5P content and forwards them to a specified Learning Record Store (LRS). 
To improve reliability, statements can be stored locally and resent later if delivery to the LRS fails.

The H5P Experience Extractor plugin was designed to complement the <a href="https://moodle.org/plugins/logstore_xapi">Logstore xAPI plugin</a> for a thorough coverage of Learning Analytics data. While the Logstore xAPI plugin captures generic Moodle events and emits them as xAPI statements, this plugin is designed to capture detailed interaction events from H5P activities.

This plugin was developed by members of the <a href="https://uni-tuebingen.de/de/80946">Hector Research Institute of Education Sciences and Psychology</a> and is openly distributed; please acknowledge the authors in any publications that use it (see citation below).

Key features
------------
- Listens for xAPI events from embedded H5P content (both Moodle or iframe).
- Sends statements to a configurable LRS endpoint.
- Stores failed statements in a Moodle DB table for later retry.
- Admin report page with:
  - List of stored statements
  - Individual resend and remove actions
  - Batch resend and "clear all" actions

Requirements
------------
- Moodle: tested on Moodle 4.5
- A Learning Record Store, for example <a href="https://www.sqllrs.com/">SQL LRS</a>
  It is important that the LRS endpoint for recieving statements follows the convention of ending in ...xapi/statements
- For complete user activity coverage additionally: <a href="https://moodle.org/plugins/logstore_xapi">Logstore xAPI plugin</a>

Installation
------------
1. Download the plugin as a zip archive 
2. Visit <i>Site administration</i> → <i>Install Plugins</i> → <i>Install Plugins</i> → <i>Install plugin from ZIP file</i>
3. Configure LRS settings either after the installation or in <i>Site administration</i> → <i>Plugins</i> → <i>local plugins</i> → <i>H5P xAPI</i> (or via the plugin settings page).

Configuration
-------------
Plugin settings (admin UI):
- <i>lrs_endpoint</i> — The base URL of your LRS (e.g. https://lrs.example.org/lrs).
- <i>lrs_username</i> — LRS credentials key.
- <i>lrs_password</i> — LRS credentials password.
- <i>id_schema</i> — Option to choose how actor IDs are mapped (username vs email). Set with the plugin settings.

Accessing the report page
-------------------------
From Moodle Plugin menu:
<i>Admin</i> -> <i>Plugins</i> -> <i>Local</i> -> <i>H5P Experience Report</i>
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

APA:<br>
Kutkina, A., & Rudzewitz, B. (2025). H5P Experience Extractor (local_h5p_xapi) [Moodle plugin]. <i>Hector Research Institute of Education Sciences and Psychology</i>. https://github.com/allakutkina/h5p_xapi

BibTeX:<br>
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
- We are open to collaboration with the community regarding the plugin.
- Please open new issues in this repository if you want to report bugs or suggest improvements. 
  Include reproducible steps and any relevant logs when reporting bugs.
