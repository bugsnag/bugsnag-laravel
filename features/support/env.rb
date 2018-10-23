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
