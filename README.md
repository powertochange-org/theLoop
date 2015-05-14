#The Loop

##Guidelines for setting up your dev environment with the Loop

1. First, clone the repo `git clone https://github.com/powertochange-org/theLoop.git`.

If you don't have git, shame on you. All jokes aside though, seriously, shame on you. BUT, if by chance you don't have git, use your Ninja-like Google-fu Skills and track down how to download it onto your machine. If you don't know how, or are too lazy to switch browser tabs to do a google search, you can use [this link](http://git-scm.com/).

2. Second, download XAMPP. No, not WAMP or MAMP. XAMPP. Pronounced xzjxzjknxjznx-AMPP. Here's [the link](https://www.apachefriends.org/index.html).

3. Do whatever else Jason or Nick or whoever else tells you to do. They're great resources for this stuff.

##Guidelines for navigating the Loop folder

Basically everything you will need to work with is going to be found in /public_html/wp-content/themes/carmel/, and if it isn't there, it will be in /themes/apps/. 

What are the different folders/files?

carmel is the theme that the Loop uses on a daily basis to display cool stuff, like everything on the site. Here is a basic rundown of carmel:

`functions.php` is the php file that handles a good chunk of basic WP functionality, and has custom functions for features that have been added over time. 

`header.php` is, well, the header for the WP Template.

`homepage.php`, as well as `form_item.php` and `forms.php`, for example, are all *body* files. These change depending on the link a Loop user has clicked on. 

`footer.php` is, you guessed it, where you play soccer. Wait. Scratch that. It's the file where the footer of the document goes.

`style.css` is the styling file, however ironic it is that the actual file filled with styles looks...well...unstylish and unruly. Maybe in time this will change.

*NOTE:* Some files and folders that affect the Loop are *not* in the Loop directory. They are in acReimbursements and AgapeConnectCentral. If you need access to them, you must ask for that access.

##Guidelines for moving forward - To Infinity, and Beyond!

![To infinity, and beyond!](http://media.giphy.com/media/oyNp7ABosl0ru/giphy.gif)

A top priority is to continue to maintain a mobile-friendly website, preferably developed for mobile-first, then desktop-second (this makes designing things easier, trust this nameless voice (until you look at who did the commit for this .md file) and realize I'm a real person).

I **highly** suggest, if you don't already know it, to learn SASS (Simply Awesome Style Sheets). I wish I had done more of this, scrapped the whole `style.css` file, and redesigned the website to look similar. The `style.css` file is truly unruly, indubitably (in-DOO-bit-a-blee).

Here is the link to [SASS](http://sass-lang.com/).

One of the major problems with the website that *needs* to be addressed still is the inline styling. It's generally bad practice (almost always), because there are always better ways to solve your CSS woes. ***Don't settle for the easiest solution if it's going to make things more difficult later.*** Go slow, take your time, learn. Smaller contributions are more useful if they make less (or no) negative future impact.

##Tips and Tricks

![Tips](http://ak.picdn.net/shutterstock/videos/771256/preview/stock-footage-tips-money-jar-ntsc.jpg) ![Tricks](http://cdn.meme.am/instances/59416816.jpg)

1. Use the [Mozilla Developer Network - MDN](https://developer.mozilla.org/en-US/) for reference regarding anything to do with HTML/CSS/JavaScript. It's really handy.

2. If you are worried about cross-browser compatibility issues, check out [Can I use](http://caniuse.com/).

3. The [WordPress Codex](http://codex.wordpress.org/) can be handy at times, but try to avoid it. Learning how to build WP Themes could help you understand a lot about what the Loop really needs/doesn't need. For this, take a look at [Theme Shaper](http://themeshaper.com/).

##That's a wrap, folks!

![Bye](http://media.giphy.com/media/k0FUxvaxaFJW8/giphy.gif)