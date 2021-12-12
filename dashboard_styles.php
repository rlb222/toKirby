<style>
/* Dashboard Pages: don't show */
div[data-app="content"]
{
	display:none;
	float: none;
}

/* Dashboard Forms: don't show */
div[data-app="perch_forms"]
{
	display:none;
	float: none;
}

.dashboard
{
  flex-flow: column;
}

.dash .reg_widget h2{
  /* font-family: "Century Gothic", "Avant Garde", Verdana, sans-serif; */
  font-size: 1rem;
  background-color: #fefcf5;

  color: #844e96;
  font-weight: normal;
}


.dashboard li a{
	color: grey;
}


.dashboard li a.selected{
	color: black;
	font-weight: bold;
}


div.SameLine{
	position: relative;
	top: -2.4em;
	display: inline;
	padding-bottom: 0;
	padding-top: 0;
}

div.SameLine label{
	position: relative;
}


div.LastLine label{
	display: none;
}


/* DashBoard */
table.pageslist th {
	height: 3em;
	vertical-align: bottom;
	text-align: left;
	width:80%;
  background-color: #fff;
}

table.pageslist td{
  padding: 4px 10px;
}


table.pageslist tr:nth-of-type(even) {
  /* background-color: #fefcf5; */
}


a.dashboardItem {
  font-size: 1.2em;
  font-family: "source-sans-pro", sans-serif;
  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
  line-height: 1.4em;
  color: #586e75;
  margin-left: 1em;
}

td.regionName{
	width: 40%;
  font-family: "source-sans-pro", sans-serif;
  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
}

a.pageName {
	/* font-family: "Century Gothic", "Avant Garde", Verdana, sans-serif; */
  font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
	color: #844e96;
	font-weight: normal;
	font-size: 1.3rem;
	line-height: 1em;
	padding-top: 1em;
}




/* Button */

input.button3
{
	display:inline-block;
	margin:0 0.3e4 0.3em 0;
	padding-left: 3rem;
	padding:0.3em 1.2em;
	border-radius:2em;
	box-sizing: border-box;
	text-decoration:none;
	font-family:'Roboto',sans-serif;
	font-weight:300;
	color:#FFFFFF;
	background-color:#0956bc;
	text-align:center;
	transition: all 0.2s;
	pointer: hand;
}

input.button3:hover{
	background-color:#0271e8;
}


/* Workspace toKirby */
div.workspace.tokirby {
	padding-top: 1rem;
	padding-left: 3rem;
	padding-bottom: 5rem;
	width: 80%;
}

form.tokirby{
	text-align: right;
}

/* Button */

input.tokirby
{
	display:inline-block;
	margin:0 0.3e4 0.3em 0;
	padding-left: 3rem;
	padding:0.3em 1.2em;
	border-radius:0.5em;
	box-sizing: border-box;
	text-decoration:none;
	font-weight:300;
	color:#FFFFFF;
	background-color:#1966bc;
	text-align:center;
	transition: all 0.2s;
	cursor: pointer;
}

input.tokirby:hover{
	background-color:#0271e8;
}



/* Einde Dashboard */
</style>