<?php


	namespace ItsMieger\Annotations\Commands;


	use Illuminate\Console\Command;
	use ItsMieger\Annotations\Cache\ClearsAnnotationCache;

	class ClearAnnotationCacheCommand extends Command
	{
		use ClearsAnnotationCache;

		/**
		 * The name and signature of the console command.
		 *
		 * @var string
		 */
		protected $signature = 'annotations:clear';

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		protected $description = 'Clears the annotations cache';


		/**
		 * Create a new command instance.
		 *
		 */
		public function __construct() {

			parent::__construct();

		}

		/**
		 * Execute the console command.
		 *
		 */
		public function handle() {

			$this->clearAnnotationCache();

			$this->info('Annotation cache cleared successfully.');

			return 0;
		}

	}