***
{
    "layout":"page2.twig",
    "title":"Page 2 Title"
}
***

{{ page.conf.layout }}

{% for page in pages %}
  {{ page.conf.layout }}-{{ page.conf.title }}
{% endfor %}

# {{ page.conf.title }}
