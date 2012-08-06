***
{
    "layout":"page.twig"
}
***

{{ conf.layout }}

{% for page in pages %}
  {{ page.getLocation }}
{% endfor %}

Lorem ipsum
===========