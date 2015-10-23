# System Pages Bundle

Use dynamic content in your controllers. When defining a route, add `_internal_name`:

```
# routing.yml

my_static_route:
    path: /example
    controller: …
    defaults: 
         _internal_name: example_page

```

If you publish a page with internal name equal to *example_page*, it will be available in `$request->attributes` and in twig:
`page` and `nodetranslation`. Standard Kunstmaan CMS functions (titles, breadcrumbs, metadata) should work out of the box.
