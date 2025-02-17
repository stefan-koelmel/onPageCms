<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>Better Coda Slider</title>
<link rel="stylesheet" href="coda-slider.css" type="text/css" media="screen" title="no title" charset="utf-8">

<script src="/includes/jquery.js" type="text/javascript"></script>
<?php
//<script src="jquery.scrollTo-1.3.3.js" type="text/javascript"></script>
//<script src="jquery.localscroll-1.2.5.js" type="text/javascript" charset="utf-8"></script>
//<script src="jquery.serialScroll-1.2.1.js" type="text/javascript" charset="utf-8"></script>
?>
<script src="jquery.coda-slider-3.0.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
    <div id="wrapper">
        <h1>jQuery Coda Slider</h1>

        <div id="intro">
            <p>This technique demonstrates an accessible 'Coda'-like slider interface, but in addition, allows you to place links to the sliding content anywhere on the page and have the effect (and navigation) still work. <br /><a href="http://jqueryfordesigners.com/coda-slider-effect">Read the article, and see the screencast this demonstration relates to</a></p>
        </div>

        <div id="slider">
            <ul class="navigation">
                <li><a href="#sites">Sites</a></li>
                <li><a href="#files">Files</a></li>
                <li><a href="#editor">Editor</a></li>
                <li><a href="#preview">Preview</a></li>
                <li><a href="#css">CSS</a></li>
                <li><a href="#terminal">Terminal</a></li>
                <li><a href="#books">Books</a></li>
            </ul>

            <div class="scroll">
                <div class="scrollContainer">
                <div class="panel" id="sites"><h2>Sites</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>
                <div class="panel" id="files"><h2>Files</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>
                <div class="panel" id="editor"><h2>Editor</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>
                <div class="panel" id="preview"><h2>Preview</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>
                <div class="panel" id="css"><h2>CSS</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>
                <div class="panel" id="terminal"><h2>Terminal</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. <a href="#sites">And some sites</a></p></div>
                <div class="panel" id="books"><h2>Books</h2><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></div>
                </div>
            </div>

            <div id="shade"></div>
        </div>

        <p>Lorem ipsum dolor sit amet, <a href="#books">books</a> consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco <a href="#sites">sites</a> laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure <a href="#terminal">terminal</a>  dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

    </div>
</body>
</html>