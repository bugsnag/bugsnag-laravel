require 'os'
require 'pp'

# Copy bugsnag-laravel into fixture directory
VENDORED_LIB = 'features/fixtures/laravel/bugsnag-laravel.zip'
`composer archive -f zip --dir=#{File.dirname(VENDORED_LIB)} --file=#{File.basename(VENDORED_LIB, '.zip')}`

# Remove any locally installed composer deps
FileUtils.rm_rf('features/fixtures/laravel/vendor')

Before do
  find_default_docker_compose
end

at_exit do
  FileUtils.rm_rf(VENDORED_LIB)
end
