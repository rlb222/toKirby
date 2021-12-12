# toKirby
 Perch Dashboard app for exporting content to (Kirby) text files and Kirby Blueprint files

- This software is in progress. 
  
  
## Install like a dashboard app
1. Copy the files from `\toKirby\` to the `\perch\addons\apps\tokirby\` folder.
2. Install Kirby Block Preview, link below.


## What the Dashboard app does
After installing, goto the perch dashboard, you see a list of all pages with regions with region-items.  
Press the button to start making an export.
The software will only export and doesn't alter anyting in your Perch install. Except from adding a folder with the exported files in `\perch\`.  
   

## The exported files
Blueprints are put in one folder: `/perch/kirby_site_name/site/blueprints/pages/`  
Content files are put folders per page in: `/perch/kirby_site_name/content/`  
Copy the files to your Kirby site.  
  
## Use in Kirby
You can install the Kirby Starter kit and copy exported Perch content into it.
Also install the plugin `Kirby Block Preview` so the repeating regions will have inline editing similar to Perch Admin behaviour.  
The Blueprint files are set up to use this Kirby Plugin.  
  
## ToDo
- images are not written correctly
- Perch-blocks export not tested
- Perch Runway not tested
- rewrite perch templates to Kirby templates (big job)





## Install Kirby Fields Block plugin

Kirby [block preview](https://getkirby.com/docs/reference/plugins/extensions/blocks) plugin to directly render block fields, allowing for inline editing.
