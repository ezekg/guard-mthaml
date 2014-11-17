# Guard::MtHaml [![Gem Version](https://badge.fury.io/rb/guard-mthaml.svg)](http://badge.fury.io/rb/guard-mthaml)
This is a Guard wrapper for [MtHaml](https://github.com/arnaud-lb/MtHaml) to compile Haml to PHP, Twig or static HTML.

## Installation
Add to your `Gemfile`:
```ruby
gem 'guard-mthaml'
```

Require in your `Guardfile`:
```ruby
require "guard/mthaml"
```

Or, add the default Guard::MtHaml template to your `Guardfile` by running:
```bash
$ guard init mthaml
```

## Usage
Requires that `php` be executable via command line.

```ruby
###
# Sample Guardfile block for Guard::MtHaml
#
# :input           ("views/src") set input directory with haml files
# :output          ("views")     set output directory for compiled files
# :environment     ("php")       haml environment
# :notifications   (true)        toggle guard notifications
# :compress_output (false)       compress compiled haml files
# :static_files    (false)       compile haml to static html
# :run_at_start    (true)        compile files when guard starts
###
guard :mthaml, :input => "views/src", :output => "views"
```

## Filters
Currently, only support for Markdown and CoffeeScript is available. I will eventually support Sass (pull requests are welcome). _MtHaml doesn't have a way of disabling the runtime variables inside of filters when attempting to use interpolation (`#{$var}`); this is a known issue that needs to be addressed within MtHaml before it can be included into this plugin._

* [CoffeeScript](https://github.com/alxlit/coffeescript-php/)
* [Markdown](https://github.com/michelf/php-markdown)

## Authors
[Ezekiel Gabrielse](http://ezekielg.com)

## License
Graphite is available under the [MIT](http://opensource.org/licenses/MIT) license.
