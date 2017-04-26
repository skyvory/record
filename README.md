# Record

Read back-end harnessed by [recomposition] (https://github.com/skyvory/recompose) front-end.  
Branching global database into expanded personal VN evaluation.  
The motives are, used by vndb, deficient and substandard evaluation/voting system with -- while 'it just works' -- outdated, cluttered (more and more these days), and rigid user interface combined with unintuitive orthodox experience.

This web app serves as prototypical design reference for future development plan which will be detached from central data processing dependence.  
The loose-coupled back-front separation will be unified thence.

## Prerequisites
1. Enable `extension=php_fileinfo.dll` in php.ini

## Preparation
1. Install [Composer] (https://getcomposer.org/download/)
2. Run `composer install` in directory consisting composer.json

## Internal convention
Whilst no hardcode restriction being applied, there are some conventions used internally. Might purposely break normalization in sake of simplification.

### Assessment
#### Status
Derived from common point of identification with unnecessary/temporary hassle omitted  
1.  playing
Indication that the title completion is on progress.
2.  halted  
Indication that the title is suspended/stalled.  
3.  finished  
Indicating completely finished tittle.  
4. dropped  
Planned to use decomposed to no avail. Common term is used instead.  

#### Node
Root base of the title's genre on the lowest level split into 2 division  
1. VN  
Targeted for full story-oriented content, preferably with low erotism.  
2. H  
Targeted for content with low-quality story and particularly from some extent to higher amount of sexual content.  
3. RPG
Role-Playing Game. Self-explanatory.
4. HRPG
Role Playing Game-featured gameplay with erotic content as its main selling-point.

### Character
#### Mark
Favoritism point in letter-grade rank towards stated character


## Dev Guidelines
### Response convention
If possible, always include these properties on every returned JSON response.
Format:
response: {
	meta: {
		message: [string:message describing whatever],
	},
	data: your data to return here
}

### Record status indication
The status of a row in a table in database which indicate the activity of the recorded data.
1. Active
2. Archived
3. Deleted
Current table that incorporate record_status column: assessments, assessments_history, vn, characters, developers, screens