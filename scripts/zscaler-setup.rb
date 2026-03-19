# Setup script for anyone working in a Zscaler environment.  This script assumes that the root certificate is present
# at ~/.zscaler-root-ca.pem and copies it into place in each of the test fixture so that the Docker builds can install
# it into the built images.  This is not need on CI or in other non-Zscaler environments.

require 'fileutils'

# Create an array of all folders under features/fixtures
certificate_path = File.expand_path("~/.zscaler-root-ca.pem")
unless File.exist?(certificate_path)
  abort("Zscaler root certificate not found at #{certificate_path}")
end
fixture_folders = Dir.glob("features/fixtures/*/")
fixture_folders.each do |folder|
  puts "Copying Zscaler root certificate to #{folder}"
  FileUtils.cp(certificate_path, folder)
end

