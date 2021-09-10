require 'net/http'

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
      case fixture
      when 'laravel-latest' then 61299
      when 'laravel66' then 61266
      when 'laravel58' then 61258
      when 'laravel56' then 61256
      else raise "Unknown laravel fixture '#{ENV['LARAVEL_FIXTURE']}'!"
      end
    end
  end
end
