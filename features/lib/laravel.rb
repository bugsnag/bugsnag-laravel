require 'net/http'
require 'yaml'

class Laravel
  class << self
    attr_reader :last_response

    def reset!
      @last_response = nil
    end

    def navigate_to(route, attempts = 0)
      @last_response = Net::HTTP.get('localhost', route, fixture_port)
    rescue => e
      raise "Failed to navigate to #{route} (#{e})" if attempts > 15

      sleep 1
      navigate_to(route, attempts + 1)
    end

    def fixture
      ENV.fetch('LARAVEL_FIXTURE', 'laravel56')
    end

    def fixture_port
      @port ||= load_port_from_docker_compose
    end

    def major_version
      # the first "canonical segment" is the first digit of the version number,
      # aka the major version
      version.canonical_segments.first
    end

    def version
      # e.g. laravel56 -> 56, lumen8 -> 8
      raw_digits = /^(?:laravel|lumen)(\d+)/.match(fixture)[1]

      # convert the raw digits to an array of: [major, minor, patch]
      # in practice we only have 1 or 2 digits in our fixture names, so fill the
      # rest with 0s to make sure Gem::Version doesn't get confused
      # e.g. ['5', '6'] -> ['5', '6', '0'], ['8'] -> ['8', '0', '0']
      version_string = raw_digits.chars
      version_string.fill("0", version_string.length..2)

      Gem::Version.new(version_string.join("."))
    end

    def lumen?
      fixture.start_with?("lumen")
    end

    def latest?
      fixture.end_with?("-latest")
    end

    def supports_sessions?
      return false if lumen?
      return false if fixture == "laravel51"

      true
    end

    def queue_worker_command(tries:)
      # the command to run the queue worker was 'queue:listen' but changed to
      # 'queue:work' in Laravel 5.3 ('queue:work' exists on older Laravels, but
      # is not quite equivalent)
      if version < '5.3.0'
        "php artisan queue:listen --tries=#{tries}"
      else
        "php artisan queue:work --tries=#{tries}"
      end
    end

    private

    def load_port_from_docker_compose
      compose_file = YAML.safe_load(File.read(Maze::Docker.singleton_class::COMPOSE_FILENAME))
      service = compose_file.fetch("services").fetch(ENV['LARAVEL_FIXTURE'])

      service.fetch("ports").first.fetch("published")
    end
  end
end
