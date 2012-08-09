## Installation

Gumdrop requires PHP 5.3 CLI.  
If you're using Linux, your distribution most likely has a PHP CLI package for you to install.  
If you're using Mac OS 10.7.x (Lion), PHP 5.3 is already installed.  
Gumdrop has not been tested on Windows.

You'll then need the latest version of [Composer](http://getcomposer.org/ "Composer").

Once it's installed, run the following:

    cd /path/to/gumdrop
    composer install

Composer will download Gumdrop's dependencies for you.

## Usage

### Markdown

Gumdrop will generate HTML files from Markdown files while keeping their folder layout. Gumdrop identifies as a Markdown file any file with a `.md` or `.markdown` extension.  
Please refer to [Markdown's documentation](http://daringfireball.net/projects/markdown/syntax "Daring Fireball: Markdown Syntax Documentation") for more information.

### Layout
Gumdrop will look for a `_layout` folder at the root of the folder containing your Markdown files.

By default, Gumdrop will try to apply the `_layout/page.twig` [Twig](http://twig.sensiolabs.org/ "Twig's documentation") template to all your pages.  
`_layout/page.twig` must at least contain the following code:

    {% raw %}{{ content }}{% endraw %}

So the bare minimal you'll need is:

    _layout/page.twig
    first_article.md

If the `_layout/page.twig` doesn't exist, by default, Gumdrop will not apply any layout and will render just the Markdown files to HTML.

A more elaborate file layout could be:

    _layout/default.twig
    _layout/page.twig
    category/second_article.markdown
    first_article.md

with `_layout/default.twig` containing:

    {% raw %}<html>
    <head>
        <title>My Website</title>
    </head>
    <body>
    {% block content %}{% endblock %}
    </body>
    </html>{% endraw %}

and  `_layout/page.twig` containing:

    {% raw %}{% extends "default.twig" %}

    {% block content %}
    <p class="page_content">
        {{ content }}
    </p>
    {% endblock %}{% endraw %}

This will include all your pages in the `default.twig` file.

Please refer to [Twig's documentation](http://twig.sensiolabs.org/doc/templates.html "Twig's documentation") for more information.

### Configuration header

Alternatively, you can set a layout template for each page.

For that, you have to create a configuration header in your Markdown page.  A configuration header should be at the top of the file, start with a line containing `***` and end with another line containing `***`.  
Inside the header, the configuration is set in a [JSON](http://www.json.org/) object.

Here's an example:

    ***
    {
        "layout":"my_custom_layout.twig"
    }
    ***
    # Title
    Some Markdown content

The header will be removed automatically before the page conversion to HTML.

Currently, the only supported configuration is `layout`.  
However, any additional property you had to the JSON configuration object will be accessible through the `page.getConfiguration` Twig variable:

    ***
    {
        "layout":"my_custom_layout.twig",
        "title":"My Page Title"
    }
    ***
    # {{ page.getConfiguration.title }}
    Some Markdown content

As you probably guessed, `{{ page.title }}` will be replaced with `My Page Title`.

### Static files
Because you will probably want to add some stylesheets, JavaScript and pictures to your layout or pages, Gumdrop will copy them over to the destination folder for you.  
It will **not** copy the Markdown files or the `_layout` folder.

### Conversion to HTML

Run the following:

    cd /path/to/gumdrop
    ./gumdrop.php /path/to/markdown/files/ /path/to/destination/folder/

## Contributions
### Forking
You can of course fork this project at your leisure.  
Any PR should be done with a dedicated branch originating from the `develop` branch.  
I will **not** merge the latest commits on the `develop` branch for you so please make sure you're up-to-date before issuing a PR.  
Anyone willing to test and fix Windows issues is welcome to do so ^^
### Coding
My coding style is pretty obvious. Please try to respect it as much as possible.

All code should be unit tested - *TDD FTW!*  
There is an additional dependency for developers, [Mockery](https://github.com/padraic/mockery). You should run your composer commands with the ``--dev`` options:

    composer install --dev
    composer update --dev
    
Unit tests are written for [PHPUnit 3.6](http://www.phpunit.de/manual/3.6/en/index.html):

    cd /path/to/gumdrop/tests/
    phpunit -c phpunit.xml
    
PHPDoc is available [here](phpdoc/index.html).

    cd /path/to/gumdrop/tests/
    phpdoc

You should use [PHPDocumentor 2](http://www.phpdoc.org/).

## License

> Copyright &copy;2012 Simon Jodet
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