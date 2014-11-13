<?php

namespace EGabrielse;

// Go up to root of plugin
require_once dirname(dirname(dirname(dirname(__DIR__)))) . "/vendor/autoload.php";

use MtHaml;
use Michelf\Markdown as Markdown;
use CoffeeScript\Compiler as CoffeeScript;

/**
 * Compile Haml files locally. Used during development stages.
 *   Can minify compiled assets for production, as well as
 *   compile inline Markdown and CoffeeScript.
 *
 * @since 0.1.0
 */
class MtHamlCompiler {

    /**
     * @var {String}
     *   Input filename that was passed into compiler
     *
     * @since 0.1.0
     */
    private $input_file;

    /**
     * @var {String}
     *   Output directory to write compiled output to
     *
     * @since 0.1.0
     */
    private $output_dir;

    /**
     * @var {String}
     *   Type of MtHaml environment, either Twig or PHP
     *
     * @since 0.1.0
     */
    private $environment;

    /**
     * @var {Array}
     *   Array of options to be passed to MtHaml
     *
     * @since 0.1.0
     */
    private $options;

    /**
     * @var {Array}
     *   Array containing Haml filters and their instances
     *
     * @since 0.1.0
     */
    private $filters;

    /**
     * @var {Object}
     *   Instance of \MtHaml\Support\Php\Executor used to compile static files
     *
     * @since 0.1.0
     */
    private $executor;

    /**
     * @var {Object}
     *   Instance of \MtHaml\Environment
     *
     * @since 0.1.0
     */
    private $compiler;

    /**
     * @var {String}
     *   Compiled output of Haml file
     *
     * @since 0.1.0
     */
    private $output;

    /**
     * Constructor
     *
     * @param {Array} $opts
     *   Array that contains input file and output directory
     *
     * @since 0.1.0
     */
    public function __construct( $opts = array() ) {

        // Set filters
        $this->filters["markdown"] = new \MtHaml\Filter\Markdown\MichelfMarkdown( new Markdown );
        $this->filters["coffeescript"] = new \MtHaml\Filter\CoffeeScript( new CoffeeScript(), array( "header" => false ) );

        /**
         * Get the input file
         */
        if( isset( $opts['input'] ) ) {
            $this->input_file = $opts['input'];
        } else {
            throw new \Exception( self::colorize( "No input file was passed into \$opts.", ";31" ) );
        }

        /**
         * Get the output dir
         */
        if( isset( $opts['output'] ) ) {
            $this->output_dir = $opts["output"];
        } else {
            throw new \Exception( self::colorize( "No output directory was passed into \$opts.", ";31" ) );
        }

        /**
         * Make sure an options map was passed
         */
        if( isset( $opts['options'] ) ) {
            $this->options = $opts['options'];
        } else {
            throw new \Exception( self::colorize( "No options were passed into \$opts.", ";31" ) );
        }

        /**
         * Get the environment
         */
        if( isset( $this->options['environment'] ) ) {
            $this->environment = $this->options['environment'];
        } else {
            throw new \Exception( self::colorize( "No environment was passed into \$opts.", ";31" ) );
        }

        // Set the MtHaml environment
        self::set_environment();
    }

    /**
     * Runs compiler and writes contents to output file
     *
     * @since 0.1.0
     */
    public function run() {
        // Compile output
        $this->output = $this->compile();
        // Compress output if set
        if( $this->options["compress_output"] ) {
            $this->output = $this->compress();
        }
        // Write output to file
        $this->write();
    }

    /**
     * Create HtHaml environment
     *
     * @since 0.1.0
     */
    private function set_environment() {
        $this->compiler = new \MtHaml\Environment( $this->environment, array(
            'enable_dynamic_attrs' => false,
            "enable_escaper" => false,
            "cdata" => false
        ), array(
            "markdown" => $this->filters["markdown"],
            "coffeescript" => $this->filters["coffeescript"],
        ));
    }

    /**
     * Instantiate MtHaml and compile passed file
     *
     * @since 0.1.0
     */
    private function compile() {
        if ( $this->options["static_files"] ) {
            // Instantiate executor based on environment
            if ( $this->environment == "php" ) {
                $this->executor = new \MtHaml\Support\Php\Executor( $this->compiler, array(
                    "cache" => $this->output_dir . '/cache',
                ));
            } else {
                throw new \Exception(self::colorize( "To compile static files, please set your environment to PHP. It is currently set to `$this->environment`.", ";31" ) );
            }
            // Compile assets
            return $this->executor->render( $this->input_file, array() );
        } else {
            // Get contents from input file and compile
            return $this->compiler->compileString( file_get_contents( $this->input_file ), basename( $this->input_file ) );
        }
    }

    /**
     * Removes all white space and newlines from output
     *
     * @TODO Find a Composer plugin that will do this, as this isn't very clean
     *
     * @since 0.1.0
     */
    private function compress() {
        return preg_replace( "/^\s+|\n|\r|\s+$/m", "", $this->output );
    }

    /**
     * Make sure that the path is writable
     *
     * @param {String} $dir
     *   Directory path
     *
     * @since 0.1.0
     */
    private function ensure_path_writable( $dir ) {
        // Attempt to change permissions if not writable
        if ( ! is_writable( $dir ) ) {
            chmod( $dir, 0760 );
        }
        // If dir is still not writable, throw err
        if ( ! is_writable( $dir ) ) {
            throw new \Exception( self::colorize( "It looks like the directory `$dir` isn't writable.", ";31" ) );
        }
    }

    /**
     * Writes compiled assets to output file
     *
     * @since 0.1.0
     */
    private function write() {
        if ( $this->output ) {
            $extension = $this->options["static_files"] ? "html" : $this->environment;
            // Make sure our path is writable
            self::ensure_path_writable( $this->output_dir );
            // Render output
            return file_put_contents( "$this->output_dir/" . basename( $this->input_file, ".haml" ) . ".$extension", $this->output );
        } else {
            throw new \Exception( self::colorize( "It looks like `" . basename( $this->input_file ) . "` compiled without any output.", ";33" ) );
        }
    }

    /**
     * Colorize output messages to terminal
     *
     * @param {String} $message
     * @param {String} $color
     *
     * @since 0.2.0
     */
    private function colorize($message, $color) {
        return "\e[0$color" . "m" . "$message\e[0m";
    }
}

/**
 * Get passed arguments from Guard
 */
$opts = getopt( "", array( "input:", "output:", "environment:", "static_files:", "compress_output:" ) );

/**
 * Instantiate compiler and parse file with input from Guard
 */
try {
    $compiler = new MtHamlCompiler( array(
        "input" => $opts["input"],
        "output" => $opts["output"],
        "options" => array(
            "environment" => $opts["environment"],
            "static_files" => $opts["static_files"] === "true" ? true : false,
            "compress_output" => $opts["compress_output"] === "true" ? true : false
        )
    ));
    $compiler->run();
} catch ( \Exception $err ) {
    exit ( $err->getMessage() . "\n" );
}
