# Conception

## Problem

Defined problem is relations collection: By parameter1 for parameter2 (set) and vice versa. Additional values and attribute levels will occur when more attibutes will be added.

## Domain Objects
- Product (lense)
- Attribute (parameter1, parameter2)

## Context

From the context (and provided example site) it's clear that:
- expected scenario is many reads (expected many requests) and few writes (slowly changing dimension)
- attributes relationship is based on products ("Depending on the configuration of contact lenses certain combinations of parameters are not allowed.")
  - products amount is limited because of special niche and limited amount of manufacturers (e.g. supplied example site contains maybe only ~150 products)
  - lense is a grouping criteria of attributes
- data set of attributes limited (sph, axis, cyl, add, color, bc, dia)
- data set of attribute values limited as well  (~250 attribute values)

Relational database is quite suitable solution to go. Sql queries using indexes (as data size is small, indexes will fit in to the memory).

\
&nbsp;

# Solutions

## 1st solution

Direct implementation of attributes relationship.

DB tables:

```
attribute
- id:int:pk
- name:varchar(63)

attribute_value:
- attribute_id:int:pk
- value:varchar(255)

attribute_relations
- from_attribute_id:int:fk
- to_attribute_id:int:fk
```

Pros:
- simple to implement and maintain
- covers structure for API calls (one level relations lookup)

Cons:
- multiple indexes required
  - index on attribute_relations (from_attribute_id, to_attribute_id)
  - index on attribute_relations (to_attribute_id, from_attribute_id)
- **hardly extendable and maintainable in long term**
  - probably more than two attributes level will be required in the future
  - additionaly filtering of single product attirbutes will be required in the future

## 2nd solution

Second iteration: including product as attributes grouping criteria (as very likelly in future attributes might be required to filter by product as well - e.g. filter attributes which are in stock).

Partially normalized DB tables:

```
product:
- id:int:pk
- name:varchar(255)

attribute:
- id:int:pk
- name:varchar(63):index

product_attribute:
- product_id:int:fk
- attribute_id:int:fk
- value:varchar(255):index
```

Pros:
- Simple environment to maintain
- Additionally it has potential in future to:
  - Filter only certain product attributes and their values
  - Fit additional attributes levels

Cons:
- Works well in low loads and low size database
- Allocates space for attribute value storage 
  - attibute values as well as attributes are repetitive for each product
- Lost slowly changing dimmension: managing attribute values becomes complicated (e.g. typo in value, requires full lookup and update)
- Might/Will require indexes usage analysis
- Additional indexes required
  - Index on attribute (id, name)
  - Relatively big index on product_attribute (value)


## 3th solution

Fully normalized DB tables:

```
product:
- id:int:pk
- name:varchar(255)

attribute:
- id:int:pk
- name:varchar(63):index

attribute_value:
- id:int:pk
- attribute_id:int:fk
- value:varchar(255):index

product_attribute:
- product_id:int:fk:index
- attribute_id:int:fk:index
- attribute_value_id:int:fk:index
```

Fully normalized database vs Non normalized database

- Making calculations for the WORST CASE scenario for **relative** disk consumption/usage:
  - assumming varchar allocates 2 bytes for a char (each char is european utf8) + 2 bytes for length definition
  - assumming varchars are filled fully
  - assuming all products are related to all attribute values
- fully normalized
  - attribute table - 7 rows * (4bytes + 128bytes) =  924 bytes
  - attribute_value table - 250 rows * (4bytes + 4bytes + 512bytes) =  130 000 bytes
  - product_attribute table - 150 products * 250 attribute = 37500 rows * (4bytes + 4bytes + 4bytes) =  450 000 bytes
  - total: 580924 bytes = 0.58mb (because of "full" varchar)
- non fully normalized (attribute values are stored within product attribute table)
  - attribute table - 7 rows * (4bytes + 254bytes) =  1806 bytes
  - product_attribute table - 150 products * 250 attribute = 37500 rows * (4bytes + 4bytes + 512bytes) =  19 500 000 bytes 
  - total: 19mb (because of "full" varchar)
- Fully normalized table **saves space nealry 40 times**
  - Means less storage required for data
  - Means smaller multiple indexes 
  - Better performance at the end

Pros:
- Simple environment to maintain
- Additionally it has potential in future to:
  - Filter only certain product attributes and their values
  - Fit additional attributes levels
- Works better in higher loads and bigger databases

Cons:
- Might/Will require indexes usage analysis
- Additional indexes required
- Query performace testing will be required


## 3A solution

Knowing real word scenarious, it's obvious couple of additional things which will occur:
- there will be visitor peeks (as this service is very likely to be public) - e.g. advertisement campaigns / events. 
- in the same database there will be more than these tables and indexes
- cpu will be derived between multiple queries

So previous solutions might be causing issues on high loads.

Caching data in the database level (e.g. materialized view). Using materialized view for queries. Refresh view in the background - eventual consistancy on data updates.

Pros:
- Performance gain compared to previous solutions on high loads
- Performance gain compared to previous solutions on increased datasets
- Expensive queries are executed only once, when view is refreshed

Cons:
- **Database complexity increases**
- **Quite static cache**
- Very likely application becomes more coupled to the selected RDBMS


## 3B solution

Putting cache layer in the application level (store in cache on demand). As some of the attributes are more common than others (I would guess), caching all of them is even not required.

Pros:
- Performance gain 
- Simple cache provider switch on/off
- Simple cache provider migration
- Resilence on the cache implementation (fallback to databse on cache provider failure)
- Possible simple feature toggling implementation on low effort 
  - Which is required on nowadays infrastructures where deployments are done multiple times a day

Cons:
- More logics in the application level
- More dependencies to maintain (cache layer - if third cache provider is used)

## Summary

I'm picking **3B** solution to implement, because of the pros defined in it's description. Beside that business logic which it adds on top is low (and way lower than materialized view implementation). 

The cache layer will be implemented using symmfony's cache abstraction / component within a decorator / chain of responsibilities patterns. 
- https://designpatternsphp.readthedocs.io/en/latest/Structural/Decorator/README.html
- https://designpatternsphp.readthedocs.io/en/latest/Behavioral/ChainOfResponsibilities/README.html

A decorator will decorate the service which will be responsible for quering data from the database. Can be easily turned off, by changing services config.

For DEMO purposes file cache will be used, in production some key=>value cache provider should be plugged in:
- memcached
- redis
- etc.

Easy changeable, by changing symfony's config to anything it supports
- https://symfony.com/doc/current/cache.html#configuring-cache-with-frameworkbundle

## Further points for improvements
- Plug-in in memory cache (key=>value), configure it with LRU
- Attributes cache warmup setup (maybe only most common used)
- Cache invalidation mechanism
- Server optimisations: Horizontal scaling of DB replicas / cache mechanisms / app

### Other calculations

```
sph: +/-20.00, stepping 0.25 = 161
axis: 180 degrees stepping 10 = 18
cyl: usually  -2.25 between -0.75, stepping 0.25 = 6
add: high, medium, low = 3
color: assuming = ~ 5 
bc: 8-10, stepping 0.1 = 20
dia: between 13 and 15, stepping 0.1 = 20
----------------------------------------------------------------
233
```