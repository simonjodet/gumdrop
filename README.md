# Gumdrop

Gumdrop is a static website generator using Markdown files and built with PHP 5.3.

## Installation

You'll need the latest version of [Composer](http://getcomposer.org/ "Composer").  
Run the following:

    cd /path/to/gumdrop
    composer install

## Usage
Gumdrop will generate HTML files from Markdown files while keeping their folder layout.  
Please refer to [Markdown's documentation](http://daringfireball.net/projects/markdown/syntax "Daring Fireball: Markdown Syntax Documentation") for more information.

Gumdrop expects a ``_layout`` folder at the root of the folder containing your Markdown files and a `page.twig` file in it.  
`_layout/page.twig` must at least contain the following code:

    {{ content }}

So the bare minimal you'll need is:

    _layout/page.twig
    first_article.md

Run the following:

    cd /path/to/gumdrop
    ./gumdrop.php /path/to/markdown/files/ /path/to/destination/folder/

A more elaborate file layout is:

    _layout/default.twig
    _layout/page.twig
    category/second_article.markdown
    first_article.md

with `_layout/default.twig` containing:

    <html>
    <head>
        <title>My Website</title>
    </head>
    <body>
    {% block content %}{% endblock %}
    </body>
    </html>

and  `_layout/page.twig` containing:

    {% extends "default.twig" %}
    
    {% block content %}
    <p class="page_content">
        {{ content }}
    </p>
    {% endblock %}

This will include all your pages in the `default.twig` file.

Please refer to [Twig's documentation](http://twig.sensiolabs.org/doc/templates.html "Twig's documentation") for more information.

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