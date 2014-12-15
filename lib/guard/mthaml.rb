require "guard"
require "guard/plugin"
require "guard/watcher"
require "pathname"

module Guard
    class MtHaml < Plugin

        ###
        # Initializer
        ###
        def initialize(options = {})
            options = {
                input: "views/src",
                output: "views",
                environment: "php",
                extension: nil,
                notifications: true,
                compress_output: false,
                static_files: false,
                run_at_start: true
            }.merge(options)

            # Define extension if environment is nil
            if options[:extension].nil?
                options[:extension] = if options[:static_files] then "html" else options[:environment] end
            end

            super(options)

            if options[:input]
                watchers << ::Guard::Watcher.new(%r{^#{options[:input]}/(.+\.haml)$})
            end
        end

        ###
        # Run at start
        ###
        def start
            run_all if options[:run_at_start]
        end

        ###
        # Stop running
        ###
        def stop
            true
        end

        ###
        # On Guard reload
        ###
        def reload
            run_all
        end

        ###
        # Run all
        ###
        def run_all
            run_on_changes Watcher.match_files(self, Dir.glob(File.join("**", "*.*")).reject { |f| f[%r{(\.php)$}] })
        end

        ###
        # Run on changes to watched files
        #
        # @param {Array} paths
        #   Paths of changed files
        ###
        def run_on_changes(paths)
            paths.each do |file|

                file = Pathname.new(file)
                file_dir = file.dirname.realpath
                input_dir = Pathname.new(options[:input]).realpath
                input_file = file.realpath

                puts file, file_dir, input_dir, input_file

                # Simple check to see if we need to create any directories in the output
                if file_dir == input_dir
                    # File looks like it's in the base directory
                    output_dir = Pathname.new(options[:output]).realpath
                else
                    # Looks like we need to create a directory or two
                    output_dir = Pathname.new(options[:output]).realpath + file_dir.to_s.gsub(input_dir.to_s, "")[1..-1]
                end

                puts output_dir

                # Make directories if they don't already exist
                make_directory(output_dir)

                # Initiate compiler
                compile_haml(input_file, output_dir)
            end
        end

        ###
        # Called when a watched file is removed
        #
        # @param {Array} paths
        #   Paths of changed files
        ####
        def run_on_removals(paths)
        end

        private

        ###
        # Wrapper to run through PHP Haml compiler, which creates new files itself
        #
        # @param {String} input
        #   Input file to pass to compiler
        # @param {String} output
        #   Output directory to pass to compiler
        ###
        def compile_haml(input, output)

            command = [
                "php #{File.dirname(__FILE__)}/mthaml/compiler/MtHaml.php",
                "--input #{input}",
                "--output #{output}",
                "--environment #{options[:environment]}",
                "--extension #{options[:extension]}",
                "--static_files #{options[:static_files]}",
                "--compress_output #{options[:compress_output]}",
            ].join " "

            begin
                throw :task_has_failed unless system command
                ::Guard::UI.info(color("write #{File.basename(input, ".*")}.#{options[:extension]}", ";32")) if options[:notifications]
            rescue StandardError => error
                ::Guard::UI.error(color("error #{File.basename(input, ".*")}.#{options[:extension]} : #{error}", ";31")) if options[:notifications]
            end
        end

        ###
        # Create dir if it doesn't already exist, used to make sure the
        #   output file structure matches the input structure
        #
        # @param {String} dir
        #   Directory to create
        ###
        def make_directory(dir)
            unless File.directory? dir
                FileUtils.mkdir_p(dir)
            end
        end

        ###
        # Set message color
        #
        # @param {String} message
        #   Text to color
        # @param {String} color
        #   Color code
        ###
        def color(message, color)
            if ::Guard::UI.send(:color_enabled?)
                "\e[0#{color}m#{message}\e[0m"
            else
                message
            end
        end
    end
end
