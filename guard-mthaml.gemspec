require_relative "lib/guard/mthaml/version"

Gem::Specification.new do |s|
    s.name = "guard-mthaml"
    s.version = Guard::MtHamlVersion::VERSION
    s.platform = Gem::Platform::RUBY
    s.authors = ["Ezekiel Gabrielse"]
    s.email = ["ezekg@yahoo.com"]
    s.homepage = "http://github.com/ezekg/guard-mthaml"
    s.summary = "Guard gem for MtHaml"
    s.description = "Guard::MtHaml automatically compiles your MtHaml template files to PHP, Twig or static HTML."
    s.license = "MIT"

    s.required_rubygems_version = ">= 1.3.6"
    s.rubyforge_project = "guard-mthaml"

    s.add_dependency "guard", ">= 1.1.0"

    s.files = Dir.glob("{lib}/**/*") + Dir.glob("{vendor}/**/*") + %w[README.md]
    s.require_path = "lib"
end
