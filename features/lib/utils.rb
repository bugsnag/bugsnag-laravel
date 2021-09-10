require "shellwords"
require "fileutils"
require "os"

class Utils
  class << self
    LIBRARY_NAME = "bugsnag-laravel"

    ##
    # @param [String] project_root the root of the bugsnag-laravel composer project
    # @param [String] directory the directory (absolute or relative to project_root) to install bugsnag into
    def install_bugsnag(project_root, directory)
      # create a tar file of the bugsnag-laravel project with "composer archive"
      Dir.chdir(project_root) do
        execute("#{composer_archive_command(directory)}")
      end

      # untar bugsnag-laravel into a new directory within the fixture
      Dir.chdir(directory) do
        Dir.mkdir(LIBRARY_NAME) unless Dir.exist?(LIBRARY_NAME)
        execute(tar_command)
      end
    ensure
      cleanup(directory)
    end

    def current_ip
      return "host.docker.internal" if OS.mac?

      ip_addr = `ifconfig | grep -Eo 'inet (addr:)?([0-9]*\\\.){3}[0-9]*' | grep -v '127.0.0.1'`
      ip_list = /((?:[0-9]*\.){3}[0-9]*)/.match(ip_addr)
      ip_list.captures.first
    end

    private

    def execute(command)
      stdout = ENV["DEBUG"] ? STDOUT : File::NULL
      stderr = ENV["DEBUG"] ? STDERR : File::NULL

      system(command, out: stdout, err: stderr, exception: true)
    end

    def composer_archive_command(directory)
      "composer archive --dir #{Shellwords.escape(directory)} --file #{LIBRARY_NAME}"
    end

    def tar_command
      "tar -xf #{LIBRARY_NAME}.tar --directory #{LIBRARY_NAME}"
    end

    def cleanup(directory)
      # delete the tar file immediately as it's no longer needed
      expected_file = "#{directory}/#{LIBRARY_NAME}.tar"
      File.delete(expected_file) if File.exist?(expected_file)

      # remove the untar-ed directory when we're about to exit
      at_exit do
        expected_directory = "#{directory}/#{LIBRARY_NAME}"

        FileUtils.rm_r(expected_directory) if Dir.exist?(expected_directory)
      end
    end
  end
end
