# config valid for current version and patch releases of Capistrano
lock "~> 3.11.0"

set :repo_url, 'https://github.com/vadimstroganov/vktest.git'

# Default value for linked_dirs is []
append :linked_dirs, "uploads"

set :keep_releases, 2

namespace :deploy do
  after :publishing, :npm_install
  after :publishing, :frontend_build

  desc 'Install NPM dependencies'
  task :npm_install do
    on roles(:app) do
      within current_path do
        execute "cd '#{release_path}/app/frontend'; npm install --silent --no-progress"
      end
    end
  end

  desc 'Frontend production build'
  task :frontend_build do
    on roles(:app) do
      within current_path do
        execute "cd '#{release_path}/app/frontend'; npm run build"
      end
    end
  end
end
