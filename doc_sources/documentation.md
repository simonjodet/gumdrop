# Quick start
<pre class="prettyprint lang-sh">
php -r "$(curl -s http://gumdropapp.com/installer.php|tail -n +2)"
</pre>
# Requirements
Gumdrop requires PHP 5.3 with CLI support. However PHP 5.4 is recommended in order to get the built-in web server option.

If you're using Linux, your distribution most likely has a PHP CLI package for you to install.  

If you're using Mac OS 10.7.x (Lion), PHP 5.3 is already installed. PHP 5.4 may require more work to get.  
You should give [Homebrew](http://mxcl.github.com/homebrew/) a try.

Gumdrop has not been tested on Windows and most probably doesn't work.

# Updates
Once in a while, you should run the following command in your Gumdrop-based project:

<pre class="prettyprint lang-sh">
php composer.phar update
</pre>

You'll get the latest features. These updates should not break backward compatibility.

# Usage

Gumdrop is intended to build any kind of web site. I use it to build this help page but also my [blog](http://blog.jodet.com/).

## Default setup
By default, if you installed Gumdrop as a Composer dependency (that is the case if you ran the "Quick start" command), Gumdrop assumes the sources are in the project folder and will generate the site in its `_site` subfolder (`<project_folder>/_site`).  

You can render your site with following command:

<pre class="prettyprint lang-sh">
_vendor/bin/gumdrop
</pre>

Two command-line options are available: `r` and `w`:

* `r` will make Gumdrop watch for any change in the source folder and render your site automatically it detects one. Hit `ctrl+C` to stop.
* `w` will start a small built-in web server. You can check your rendered site at [http://localhost:8000](http://localhost:8000). Hit `ctrl+C` to stop.

Both options can be combined:

<pre class="prettyprint lang-sh">
_vendor/bin/gumdrop -rw
</pre>


## Separated Gumdrop install
If you installed Gumdrop manually, you can pass it the source folder and target folder paths with the following options:

* `-s doc_sources/` tells Gumdrop where to find the source files,
* `-t doc/` tells Gumdrop where to write the generated site,

## Configuration
The first thing Gumdrop is looking for in the source folder is a `conf.json` file. It will complain if it can't find it.  
So make sure it exists and it contains valid [JSON](http://www.json.org/) syntax. Again, if you used the "Quick start" command, you should already have one.

The available configuration options are:

* `timezone` Sets the time zone for all date operations in Gumdrop, including Twig date functions (more on Twig below). Refer to [this page](http://php.net/manual/en/timezones.php) for the supported timezones.
* `blacklist` A JSON array of files Gumdrop should ignore when looking for files. The default version contains a blacklist to ignore Composer files (used to handle Gumdrop's dependencies).
* `destination` If you want to set a custom destination path but don't want to pass it with the command-line option every time, you can set it here.

You're free to add other site-wide configuration entries in this file. They will be available in your Twig templates in the `{{ '{{' }} site {{ '}}' }}` variable.

### Example
`conf.json`:

<pre class="prettyprint lang-js">
{
  "timezone":"Europe/Paris",
  "blacklist":["not_converterted_file.twig"],
  "site_title":"Gumdrop-generated site"
}
</pre>

## Pages
At its root, Gumdrop is a Markdown converter. Gumdrop identifies any file with a `.md` or `.markdown` extension as one of your site's __page__.

Please refer to the [Markdown's documentation](http://daringfireball.net/projects/markdown/syntax) for more information on how to write Markdown files.

Gumdrop will look for Markdown files in any subfolder of the source folder. The generated HTML files will be saved with the same relative path in the target folder.  
The only Markdown files Gumdrop will ignore are:

* the files declared in the `conf.json` file (`blacklist`),
* if the "root" folder containing the file starts with the `_` character:  
  `_folder/subfolder/file.md` will be ignored, `folder/_subfolder/file.md` will not.

### Page configuration
Every page can contain a __configuration header__. A configuration header should be at the top of the file, start with a line containing `***` and end with another line containing `***`.
Inside the header, the configuration is set in a JSON object. The header will be removed automatically before the page is converted to HTML.

Here's an example from my blog:

<pre class="prettyprint lang-js">
***
{
    "title":"Mixing up TDD, PHPUnit, Namespaces and Jenkins",
    "date":"2011-02-22",
    "category":"development",
    "layout":"post.twig",
    "target_name":"my_article.html",
    "tags":["TDD", "PHPUnit", "namespace", "Jenkins", "PHP"]
}
***
# Heading
Some Markdown content
</pre>

Some configuration keys are used by Gumdrop:

* `layout` tells Gumdrop to use this Twig template instead of the default one for this page (more on page layout below).
* `target_name` tells Gumdrop to use this information as the name of the generated file instead of guessing it from the Markdown file name.

__Note:__ More configuration keys may be used by Gumdrop in the future.

Similarly to the site configuration, you're free to had other page-wide configuration entries in the header. They will be available in your Twig templates in the `{{ '{{' }} page {{ '}}' }}` variable. However some keys are reserved as Gumdrop set them automatically: 

* `page.location` the page's relative path (and relative URL),
* `page.html` the page's converted HTML content (with the applied layout),
* `page.markdown` the page's original Markdown content.

If you set a configuration value with those reserved keys, they will be overwritten. You've been warned ;)

### Page layout
Markdown conversion to HTML is nice but you end up with pretty sad-looking web pages.

As you probably guessed by now, Gumdrop uses the [Twig](http://twig.sensiolabs.org/) template engine to help you customize and design your pages.

By default, Gumdrop is looking in the `_layout` folder in the source folder for a `page.twig` template to apply to your pages. As seen above, you can use the page's configuration header to set a different layout file.

At the bare minimum, your layout file should contain the following code:

    {{ '{{' }} content {{ '}}' }}

`{{ '{{' }} content {{ '}}' }}` is the Twig variable containing the HTML conversion of your page. You're free to surround it with HTML, JS, CSS and Twig as much as you wish.

I've put in [a Gist](https://gist.github.com/3749184) a simplified version of my blog's layout files. All post pages have a `layout` configuration set to `post.twig`.  
And Twig template inclusion works like a charm - note the `{{ '{%' }} extends "default.twig" {{ '%}' }}` in `post.twig` and `{% block content %}{% endblock %}` in `default.twig`. So my posts are included in the `default.twig` template as the index page (I'll come back later on how I build the index page).

For more information about what Twig is capable of, please refer to [its documentation](http://twig.sensiolabs.org/documentation).

## Static files
HTML files are nice but you'll probably want to put your styles in a `.css` file, your JavaScript code in a `.js` file and maybe add some images.

By default, Gumdrop will copy over any file that is not a Markdown file at the same relative path in the target folder.  
You want to put your JavaScript code in `js/script.js`? Create the file in source folder, Gumdrop will copy it as it is in the target folder.

The only exception is common to the Markdown files listing. "Root" folders starting with `_` are ignored. Files in `_layout/` or `_private/` and their subfolders will be ignored.

## Twig files
We saw before how to use Twig files as layout for your pages. That's nice but sometimes not sufficient.  
In some cases, you want to create pages where the content comes from your Markdown files. The best example: your blog's index page. It contains posts written in the Markdown files but no redactional content on its own. Same for your RSS feed.

To solve this, Gumdrop will look for Twig files (files having a `.twig` extension) outside of the `_layout` folder and render them.

But that's not be enough to create your index page. Gumdrop passes also most of your site's content to your Twig pages through variables:

* `site` an object containing the site's configuration,
* `pages` an array of objects of your site's pages:
  * `page.location` the page's relative path (and relative URL),
  * `page.html` the page's converted HTML content (with the applied layout),
  * `page.markdown` the page's original Markdown content,
  * every configuration values you set in the page's configuration header (like date, tags, title in the "Page configuration" example above).

So, by [looping](http://twig.sensiolabs.org/doc/tags/for.html) on the `pages` variable, you can easily build [an index page](https://gist.github.com/3749322).

Similar to Markdown files and static files, Twig files are converted at the same relative path in the target folder. Twig files can't contain a configuration header. However, the Twig file will be rendered to a file with the same name but without the `.twig` extension.  
So the `index.htm.twig` file will be rendered to `index.htm` and the `rss/atom.xml.twig` files will be rendered to `rss/atom.xml`.

# Contributing
## Forking
You can of course fork [this project](https://github.com/simonjodet/gumdrop/tree/develop) at your leisure.  
Any PR should be done with a dedicated branch originating from the `develop` branch.  
I will **not** merge the latest commits on the `develop` branch for you so please make sure you're up-to-date before issuing a PR.  
Anyone willing to test and fix Windows issues is welcome to do so ^^
## Coding
My coding style is pretty obvious. Please try to respect it as much as possible.

All code should be unit tested - *TDD FTW!*  
There is an additional dependency for developers, [Mockery](https://github.com/padraic/mockery). You should run your composer commands with the ``--dev`` options:

<pre class="prettyprint lang-sh">
composer install --dev
composer update --dev
</pre>
    
Unit tests are written for [PHPUnit 3.7](http://www.phpunit.de/manual/3.7/en/index.html):

<pre class="prettyprint lang-sh">
cd /path/to/gumdrop/tests/
phpunit -c phpunit.xml
</pre>
    
# License

> Copyright &copy;2013 Simon Jodet
>
> Permission is hereby granted, free of charge, to any person obtaining a copy of
> this software and associated documentation files (the "Software"), to deal in
> the Software without restriction, including without limitation the rights to
> use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
> of the Software, and to permit persons to whom the Software is furnished to do
> so, subject to the following conditions:
>
> The above copyright notice and this permission notice shall be included in all
> copies or substantial portions of the Software.
>
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
> SOFTWARE.