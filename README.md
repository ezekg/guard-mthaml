# Guard::MtHaml [![Gem Version](https://badge.fury.io/rb/guard-mthaml.svg)](http://badge.fury.io/rb/guard-mthaml)
This is a Guard wrapper to compile Haml to PHP, Twig or static HTML.

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
# :input           ("views/src") set output directory for compiled files
# :output          ("views")     set input directory with haml files
# :environment     ("php")       haml environment
# :notifications   (true)        toggle guard notifications
# :compress_output (false)       compress compiled haml files
# :static_files    (false)       compile haml to static html
# :run_at_start    (true)        compile files when guard starts
###
guard :mthaml, :input => "views/src", :output => "views"
```
