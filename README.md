# toKirby
 Perch Dashboard app for exporting content to (Kirby) text files and Kirby Blueprint files.
 You can easily install and test it in a few steps.

- Made public because of a request from Perchology Slack. At this moment it's here to get a general idea of what it should do. You can test it, there will be content, but it's not production ready.   
- This software is in progress, all feedback and help is welcome.   
  
  
## Install Perch dashboard app
1. Copy the files from `\toKirby\` to the `\perch\addons\apps\tokirby\` folder.
2. Start Perch, goto the dashboard
   
  
## Installation Kirby
1. Install Kirby, for example https://github.com/getkirby/starterkit
2. Install Kirby Fields Block: [Kirby Fields Block](https://github.com/jongacnik/kirby-fields-block).
3. Copy the file `site.yml` from this addon to the root of the Kirby Blueprints folder: `/kirby_site_name/site/blueprints/site.yml` in order to directly see the generated pages in the panel. All pages have the status 'unlisted'.


## What the Dashboard app does
After installing, goto the perch dashboard, you see a list of all pages with regions with region-items.  
Press the button to start making an export.
The software will only export and doesn't alter anyting in your Perch install. Except from adding a folder with the exported files in `\perch\`.  
   

## The exported files
Blueprints are put in one folder: `/perch/kirby_site_name/site/blueprints/pages/`  
Content files are put in folders per page: `/perch/kirby_site_name/content/`  
Copy the files to your Kirby site.  
  
## Use in Kirby
You can install the Kirby Starter kit and copy exported Perch content into it.

Also install the plugin `Kirby Fields Block` so the repeating regions will have inline editing similar to Perch Admin behaviour.  
The Blueprint files are set up to use this Kirby Plugin.  

Also copy the file `site.yml` from this installation to overwrite the one in Kirby `/blueprints/site.yml`.


## ToDo
- images are not written correctly
- Perch-blocks export not tested
- Perch Runway not tested
- For the frontend: rewrite perch templates to Kirby templates (big job)



## Screenshots
  
### Perch Dashboard
<img src="/screenshots/toKirby_dashboard.png" width="600">

### List of Pages to export
<img src="/screenshots/toKirby_pagelist.png" width="600">

### Kirby Panel with imported pages
<img src="/screenshots/kirby_panel.png" width="600">



### Installing Kirby Fields Block plugin

[Kirby Fields Block](https://github.com/jongacnik/kirby-fields-block) plugin to directly render block fields, allowing for inline editing.

