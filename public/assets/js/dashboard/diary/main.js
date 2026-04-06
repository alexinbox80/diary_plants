import configure from './config/configure.js';
import DiaryController from './controller/diaryController.js';

if (configure.debug) {
    console.log('Load Diary app()');
}

new DiaryController();
