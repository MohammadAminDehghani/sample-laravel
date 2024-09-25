<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Professor;
use App\Models\ProfessorDetails;
use App\Models\University;
use App\Services\OpenAIService;
use Exception;
use Illuminate\Http\Request;
use App\Services\WebScraperService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class WebScraperController extends Controller
{
    protected WebScraperService $webScraperService;
    protected OpenAIService $openAIService;

    public function __construct(WebScraperService $webScraperService, OpenAIService $openAIService)
    {
        $this->webScraperService = $webScraperService;
        $this->openAIService = $openAIService;
    }

    // save department html file (professores page) to prepare for API_to_AI
    public function read_and_write_all_departments_with_university_url()
    {
        $universities = University::all();

        foreach ($universities as $university) {
            $faculties = $university->faculties()->get();
            foreach ($faculties as $faculty) {
                $departments = $faculty->departments()->get();
                foreach ($departments as $department) {
                    $this->webScraperService->scrapeDepartment($department);

//                    $urlWithoutHtml = preg_replace('/\.html$/', '', $department->url);
//                    $newUrl = $urlWithoutHtml . '/about/faculty-members.html';
//                    $department->update(['professors_url' => $newUrl]);
                }
            }
        }
        dd('end-step2');
    }

    /*
        public function read_all_professors_page_with_ai(Department $department)
        {
    //        $content = 'Please read the webpage at the following URL'.
    //            'and return a list of professors as an array of arrays'.
    //            'Each professor should have an array that include first_name, last_name and absolute URL to his/her page.'.
    //            'use HTML file to retrieve me professor href to his/her page'.
    //            'The URL to analyze is: '.$department->professors_url;

    //        $content = "Please analyze the webpage at the following URL:".$department->professors_url.
    //                    "I need a list of professors on this page.".
    //                    " For each professor, provide their first name, last name,".
    //                    " and the absolute URL to their individual profile page.".
    //                    "If the webpage uses relative URLs or fragment identifiers".
    //                    " (e.g., `#professor-name`) to link to professor profiles,".
    //                    " please convert these into absolute URLs following the pattern used by the website.".
    //                    " The absolute URL should directly point to the professor's profile page.".
    //                    "For example, if the base URL is `https://www.university.edu`, ".
    //                    "and a professor is linked via a fragment like `#john-doe`, ".
    //                    "the expected output should be `https://www.university.edu/john-doe.html` ".
    //                    "(adjusting based on the actual pattern used by the university).".
    //                    "Please return the list of professors as an array of objects,".
    //                    "with each object containing the professor's first name, last name, and absolute URL to their profile.";

            //$content = "Please analyze the webpage at the following URL:".$department->professors_url.
            $content = "Please analyze the webpage at the following HTML text:".$department->professors_url.
    " I need a list of professors on this page. For each professor, please inspect the HTML to find the `href` attribute associated with their profile link. Use this to construct an absolute URL to their profile page.
    If the absolute `href` is exist give me.  If not the `href` is a relative URL or includes a fragment (e.g., `#professor-name or @`), convert it to an absolute URL by combining it with the base URL of the page. The final URL should be fully qualified, pointing directly to the professor's profile page, not just a fragment on the current page.
    Please return the list of professors as an array of objects, with each object containing the professor's first name, last name, and the correct absolute URL to their profile.";
            //dd($content);
            $professors = $this->openAIService->getResponseFromURL($content);
            //$professors = $professors['choices'][0]['message'];
            dump($professors);
            foreach ($professors as $professor_data){
                dump($professor_data);
    //            $professor = ProfessorDetails::firstOrCreate(
    //                ['first_name' => $professor_data],
    //                ['publications' => 1, 'research' => '11:30']
    //            );
            }
        }
    */


    //Todo It needs a command to run this funstion for all department by the time
    //and it's necessary to check if department professors are update or not'
    public function create_professors_by_department(Department $department)
    {

        $cleaned_html = $this->webScraperService->cleanHtmlForAI($department->professors_url);

        $content = "Please analyze the webpage at the following HTML text:" . $cleaned_html .
            "I need a list of professors on this page. For each professor,
            please inspect the HTML to find the `href` attribute associated with their profile link.
            Use this to construct an absolute URL to their profile page.
            If the absolute `href` is exist give me.
            If not the `href` is a relative URL or includes a fragment (e.g., `#professor-name or @`),
            convert it to an absolute URL by combining it with the base URL of the page.
            The final URL should be fully qualified, pointing directly to the professor's profile page,
            not just a fragment on the current page.
            Please return the list of professors as an array of objects,
            with each object containing the professor's first name, last name,
            and the correct absolute URL to their profile. use these keys: first_name, last_name, url";

        // pay _$_ //
        $professors_list_json_string = $this->openAIService->API_to_AI($content);

        $this->importProfessors($professors_list_json_string, $department->id);

    }

    public function create_professor_data_by_professor_page(Professor $professor)
    {

        $cleaned_html = $this->webScraperService->cleanHtmlProfessorPageForAI($professor->url);
        //dd($cleaned_html);
        //$cleaned_html = '';

//        "this data will be saved in DB without human checking,
//             So do not create something by guessing (like image of professor).
//             For example if publications do not have links, use empty string not writing example.com.
//             you can just create gender by name and summary by another information.
//             for another data, if you find it, use it, if not, use empty string
//             I need this professor's data.
//             please give me a response as string like this example with my data:"

//                          "image_url": "https://www.example.com/image.jpg
//                  (give me the src in the URL of image of professor, I do not want your guess!,
//                  I want Exact address, so use html file and find it.
//                  please give me this one where shows professor profile image)
//                  ",


        $content =
            "This data will be saved in a database without human validation.
    Do not guess or create any data, especially for the professor's image URL or other fields.

    For example:
    - If a publication does not have a link, use an empty string instead of inserting something like 'example.com'.
    - Only use the 'src' URL of the professor's profile image if it is explicitly found in the HTML file. Do not generate or guess a URL.
    - You can infer gender based on the professor's name and create the summary using other available information.
    - For any other data, if it's available in the HTML, use it. Otherwise, insert an empty string.

    I need the professor's data in the following format (replace the placeholder information with actual data from the provided HTML):

    {
      \"first_name\": \"John\",
      \"last_name\": \"Doe\",
      \"summary\": \"John Doe is a distinguished professor of Computer Science specializing in artificial intelligence and machine learning.\",
      \"affiliation\": \"Example University\",
      \"gender\": \"Male\",
      \"email\": \"john.doe@example.com\",
      \"phone\": \"+1-234-567-8901\",
      \"address\": \"123 Example St, Example City, EX 12345\",
      \"image_url\": \"https://www.example.com/image.jpg (Only provide the exact 'src' URL of the professor's profile image found in the HTML. If no image is found, use an empty string.)\",
      \"education\": [
        \"PhD (Example University)\",
        \"MSc (Another University)\"
      ],
      \"publications\": [
        {
          \"title\": \"Advancements in AI Technology\",
          \"journal\": \"AI Journal\",
          \"year\": 2022,
          \"link\": \"https://www.example.com/publication1 (If no link is found, leave it as an empty string.)\"
        },
        {
          \"title\": \"Machine Learning Algorithms\",
          \"journal\": \"ML Journal\",
          \"year\": 2021,
          \"link\": \"https://www.example.com/publication2\"
        }
      ],
      \"courses\": [
        \"Introduction to Artificial Intelligence\",
        \"Advanced Machine Learning\"
      ],
      \"awards_and_honors\": [
        \"Best Paper Award, AI Conference 2022\",
        \"Distinguished Researcher Award, Example University 2021\"
      ],
      \"external_links\": {
        \"website\": \"http://www.johndoe.com\",
        \"university-site\": \"http://exampleuniversity.edu/johndoe\",
        \"linkedin\": \"https://linkedin.com/in/johndoe\",
        \"twitter\": \"https://twitter.com/johndoe\"
      },
      \"professional_memberships\": [],
      \"supervision\": [],
      \"news_and_media\": [
        \"Research on AI Trends Featured in Example Magazine\",
        \"Interview on Machine Learning Innovations\"
      ]
    }.

    The HTML text provided is my data source: ". $cleaned_html;



        $this->save_file_for_log('html/ai_receive', $cleaned_html, $professor->id);

        // Remove extra whitespace
        $content = preg_replace('/\s+/', ' ', $content);

        // pay _$_ //
        //dd($content);
        $professor_details_json_string = $this->openAIService->API_to_AI($content);

        //$professor_details_json_string = '';
        $this->save_file_for_log('html/ai_response', $professor_details_json_string, $professor->id);

        $this->importProfessorDetails($professor_details_json_string, $professor);

    }

    public function importProfessors(string $json_string, int $department_id)
    {

        // Decode the JSON string into an array of associative arrays
        $professors = json_decode($json_string, true);

        foreach ($professors as $professorData) {
            $professor = Professor::where('url', $professorData['url'])
                ->orWhere(function ($query) use ($professorData) {
                    $query->where('first_name', $professorData['first_name'])
                        ->where('last_name', $professorData['last_name']);
                })
                ->first();

            if ($professor) {
                // Update the existing record
                $professor->update([
                    'first_name' => $professorData['first_name'],
                    'last_name' => $professorData['last_name'],
                    'url' => $professorData['url'],
                ]);
            } else {
                // Create a new record
                Professor::create([
                    'first_name' => $professorData['first_name'],
                    'last_name' => $professorData['last_name'],
                    'url' => $professorData['url'],
                    // Add other default or null values for the fields
                    'department_id' => $department_id // Replace with your department logic
                ]);
            }
        }

        return response()->json(['message' => 'Professors imported successfully']);
    }

    public function importProfessorDetails(string $json_string, Professor $professor)
    {
        //$json_string = '***Important*** this data will be saved in DB without human checking, So do not create something by guessing. For example if publications do not have links, use empty string not writing example.com. you can just create gender by name and summary by another information. for another data, if you find it, use it, if not, use empty string ***Important***{ "first_name": "Grant", "last_name": "Brown", "summary": "Grant Brown is a distinguished professor of Biology focusing on aquatic behavioural and chemical ecology.", "affiliation": "Concordia University", "gender": "Male", "email": "grant.brown@concordia.ca", "phone": "", "address": "", "image_url": "https://www.concordia.ca/content/concordia/en/faculty-profiles/bibe/grant-brown/_jcr_content/profileImage/file.img.jpg", "education": [ "PhD (Memorial University of Newfoundland)" ], "publications": [ { "title": "Uncertain foraging opportunities and predation risk", "journal": "Animal Behaviour", "year": "in press", "link": "" }, { "title": "Exploratory decisions of Trinidadian guppies", "journal": "Animal Cognition", "year": "in press", "link": "" } ], "courses": [ "Evolution Behavioural Ecology" ], "awards_and_honors": [], "external_links": { "website": "https://sites.google.com/site/brownlabhome/", "university-site": "http://explore.concordia.ca/grant-brown", "linkedin": "", "twitter": "" }, "professional_memberships": [], "supervision": [], "news_and_media": [ "Research on ecological uncertainty featured on Animal Behaviour", "Interview on aquatic behavioural ecology innovations" ] }';

        // Decode the JSON string into an array of associative arrays
        $professor_detail = json_decode($json_string, true);
//        dump($json_string);
//        dd($professor_detail);

/*        $professor_detail = [
            "first_name" => "Christopher",
            "last_name" => "Brett",
            "summary" => "croscopy at Concordia. He holds a Concordia University Research Chair in A",
            "affiliation" => "Concordia University",
            "gender" => "",
            "email" => "Christopher.Brett@concordia.ca",
            "phone" => "",
            "address" => "",
            "image_url" => "https://www.concordia.ca/etc/designs/concordia/resources/file.7716.jpg",
            "education" => [
                0 => "PhD (Johns Hopkins University)",
                1 => "PDF (University of Washington)",
            ],
            "publications" => [
                0 => [
                    "title" => "The intralumenal fragment pathway mediates ESCRT-independent surface transporter down-regulation",
                    "journal" => "Nature Communications",
                    "year" => 2018,
                    "link" => "https://www.example.com/publication1",
                ],
                1 => [
                    "title" => "Rab-effector-kinase interplay regulates intralumenal fragment formation during lysosome fusion",
                    "journal" => "Developmental Cell",
                    "year" => 2018,
                    "link" => "https://www.example.com/publication2",
                ],
                2 => [
                    "title" => "The Na(K)/H exchanger Nhx1 controls multi vesicular body-vacuolar lysosome fusion",
                    "journal" => "Molecular Biology of the Cell",
                    "year" => 2018,
                    "link" => "https://www.example.com/publication3",
                ],

            ],
            "courses" => [
                0 => "Cellular Neuroscience (BIOL 474/632E)",
                1 => "Comparative Animal Physiology (BIOL 382)"
            ],
            "awards_and_honors" => [
                0 => "University Research Fellow, 2019",
                1 => "Dean's Award for Excellence in Scholarship, 2019",
                2 => "Distinguished Alumnus Lecture, Johns Hopkins University (Baltimore, USA), 2019",
                3 => "Canada Research Chair Tier 2, 2011-2016",
            ],
            "external_links" => [
                "website" => "http://www.brettlab.org",
                "university-site" => "http://explore.concordia.ca/christopher-brett",
                "linkedin" => "https://www.linkedin.com/in/christopher-brett-734025177/",
                "twitter" => "https://twitter.com/drbrettphd",
            ],
            "professional_memberships" => [],
            "supervision" => [],
            "news_and_media" => [
                0 => "Discover how Synthetic Biology is reshaping our world at 4th SPACE, Concordia NOW (2020)",
                1 => "Concordia celebrates the exceptional research achievements of 9 faculty members, Concordia NOW (2019)",
            ],
        ];*/


//        Professor::where('id', $professor->id)->update($professor_detail);

        // Get only the fillable attributes
        $fillableFields = $professor->getFillable();

        // Filter the input data to match only the fillable fields
        $filteredData = array_filter($professor_detail, function ($key) use ($fillableFields) {
            return in_array($key, $fillableFields);
        }, ARRAY_FILTER_USE_KEY);

        //dd($filteredData);

        $professor->update($filteredData);



        $professor_detail['department_id'] = $professor->department_id;
        $professor_detail['professor_id'] = $professor->id;
        ProfessorDetails::updateOrCreate( ['email'=>$professor_detail['email'] ], $professor_detail );
        dd('I Love you4');


        return response()->json(['message' => 'Professors imported successfully']);
    }

    public function test()
    {
        $prof = Professor::where('id', 4)->first();
        //dd(ProfessorDetails::all());
        $this->webScraperService->scrapeProfessor($prof);
//        //dd($prof);
        $this->create_professor_data_by_professor_page($prof);
    }

    public function test2(){
        $content = Storage::get('html/ai_response/2.txt');
        $content = $this->convertToJson($content);
        $professor_detail = json_decode($content, true);
        dump($content);
        dd($professor_detail);
    }

    protected function save_file_for_log(string $pre_address, string $text, int $id)
    {
        $filename = $pre_address.'/' . $id . '.txt';
        Storage::disk('local')->put($filename, $text);
    }

    function convertToJson($content) {

        // Remove everything before the first '{' and after the last '}'
        $start = strpos($content, '{');
        $end = strrpos($content, '}');

        if ($start === false || $end === false || $start > $end) {
            throw new Exception('Invalid JSON format');
        }


        // Replace single quotes with double quotes
        $content = preg_replace("/'/", '"', $content);


        dd($content);
        // Use json_decode
        $data = json_decode($content, true);

        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON Error: ' . json_last_error_msg());
        }

        return $data;
    }

}

