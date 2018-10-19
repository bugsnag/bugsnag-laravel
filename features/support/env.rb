require 'os'

Before do
  find_default_docker_compose
end

def install_current_branch(fixture)
  # Find the current branch, replace the required laravel version with that branch name
  branches = `git branch`.split("\n")
  current_branch = branches.map{ |line| /\*\s(.*)/.match(line)}.compact.first
  branch_name = current_branch.captures.first
  branch_name.sub! '/', '\/'
  Dir.chdir "features/fixtures/#{fixture}" do
    `cat composer.json.base | sed 's/TEST_BRANCH_NAME/#{branch_name}/g' > composer.json`
  end
end

def current_ip
  if OS.mac?
    'host.docker.internal'
  else
    ip_addr = `ifconfig | grep -Eo 'inet (addr:)?([0-9]*\\\.){3}[0-9]*' | grep -v '127.0.0.1'`
    ip_list = /((?:[0-9]*\.){3}[0-9]*)/.match(ip_addr)
    ip_list.captures.first
  end
end