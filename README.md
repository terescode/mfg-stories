# Manufacturing Stories Site
This repository contains the assets, configuration, and code for the https://www.manufacturingstories.com WordPress site.

## Prerequisites
The development process and CI pipeline for this project utilize the following.

1. Git/Github for source control and issue tracking (obviously)
1. [Varying Vagrant Vagrants](https://varyingvagrantvagrants.org/) with a custom site using [vvv-custom.yml](https://varyingvagrantvagrants.org/docs/en-US/adding-a-new-site/) for local development
1. [Composer](https://getcomposer.org) for project dependency management
1. [NPM](https://npmjs.com) and [Grunt](https://www.gruntjs.com) for local task automation
1. [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) and [PHPMD](https://phpmd.org) for coding standards and static analysis
1. [DeployHQ](https://www.deployhq.com) for continuous deployment
1. [Flywheel](https://getflywheel.com) with a staging and production site for site delivery.

Local development is optimized for [Atom](https://atom.io) but uses [EditorConfig](http://editorconfig.org) for standard development in any IDE supporting EditorConfig.

## Getting Started

### Install VVV
1. Follow [these instructions](https://varyingvagrantvagrants.org/docs/en-US/installation/software-requirements/) to install the software prerequisites for VVV.
1. Install VVV as directed [here](https://varyingvagrantvagrants.org/docs/en-US/installation/)
1. Start VVV as directed [here](https://varyingvagrantvagrants.org/docs/en-US/installation/) using `vagrant up`

### Add the `mfgstories` site
1. Navigate to the directory where you cloned VVV into, e.g. `cd ~/vagrant-local`
1. Create a new `vvv-custom.yml` file using `cp vvv-config.yml vvv-custom.yml`
1. Add the following lines within the `sites:` section of `vvv-custom.yml`
		```yml
		  # CUSTOM SITE for mfgstories
		  mfgstories:
		    repo: https://github.com/terescode/mfgstories.git
		    hosts:
		      - local.mfgstories.com
		```

		*NOTE:* Whitespace matters in YML.  It is important that this snippet be inserted beneath the `sites:` with proper indentation.  The final `vvv-custom.yml` file should look something like the following.

		```yml
		sites:
		  # The wordpress-default configuration provides a default installation of the
		  # latest version of WordPress.
		  wordpress-default:
		    repo: https://github.com/Varying-Vagrant-Vagrants/vvv-wordpress-default.git
		    hosts:
		      - local.wordpress.dev

		  # The wordpress-develop configuration is useful for contributing to WordPress.
		  wordpress-develop:
		    repo: https://github.com/Varying-Vagrant-Vagrants/vvv-wordpress-develop.git
		    hosts:
		      - src.wordpress-develop.dev
		      - build.wordpress-develop.dev

		  # The following commented out site configuration will create a standard WordPress
		  # site in www/example-site/ available at http://my-example-site.dev.

		  # CUSTOM SITE for mfgstories
		  mfgstories:
		    repo: https://github.com/terescode/mfgstories.git
		    hosts:
		      - local.mfgstories.com

		  # The following commented out site configuration will create a environment useful
		  # for contributions to the WordPress meta team:

		  #wordpress-meta-environment:
		  #  repo: https://github.com/WordPress/meta-environment.git

		utilities:
		  core:
		    - memcached-admin
		    - opcache-status
		    - phpmyadmin
		    - webgrind
		```
1. Save `vvv-custom.yml`
1. Now re-provision VVV using `vagrant reload --provision` (This will take a while)
1. Once provisioning is complete, perform the following steps.
```sh
cd www/mfgstories
git remote set-url origin git@github.com:terescode/mfgstories.git
```

### Activate mfgstories and apply updates
1. Open your browser and load up the new mfgstories site at `http://local.mfgstories.com/wp-admin`
1. Login using your WP credentials
1. Navigate to `http://local.mfgstories.com/wp-admin/plugins.php`
1. Locate the plugin `Manufacturing Stories Site` and click on `Activate` to activate the plugin
1. Now navigate to `http://local.mfgstories.com/wp-admin/options-general.php?page=mfs_options` and click on `Apply all updates`

You are now ready to go!
