<?php

class Travel
{
  public $id;
  public $createdAt;
  public $employeeName;
  public $departure;
  public $destination;
  public $price;
  public $companyId;

  public function __construct($data)
  {
    $this->id = $data['id'];
    $this->createdAt = $data['createdAt'];
    $this->employeeName = $data['employeeName'];
    $this->departure = $data['departure'];
    $this->destination = $data['destination'];
    $this->price = floatval($data['price']);
    $this->companyId = $data['companyId'];
  }
}

class Company
{
  public $id;
  public $createdAt;
  public $name;
  public $parentId;
  public $cost = 0;
  public $children = [];

  public function __construct($data)
  {
    $this->id = $data['id'];
    $this->createdAt = $data['createdAt'];
    $this->name = $data['name'];
    $this->parentId = $data['parentId'];
  }
}

class TestScript
{
  public function execute()
  {
    $start = microtime(true);

    $companiesData = json_decode(file_get_contents('https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies'), true);
    $travelsData = json_decode(file_get_contents('https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels'), true);

    $travels = [];
    foreach ($travelsData as $travelData) {
      $travels[] = new Travel($travelData);
    }

    $companies = [];
    foreach ($companiesData as $companyData) {
      $companies[] = new Company($companyData);
    }

    foreach ($travels as $travel) {
      $currentCompany = $this->findCompanyById($companies, $travel->companyId);
      while ($currentCompany) {
        $currentCompany->cost += $travel->price;
        $currentCompany = $this->findCompanyById($companies, $currentCompany->parentId);
      }
    }

    $result = [];
    foreach ($companies as $company) {
      if ($company->parentId === '0') {
        $result[] = $this->buildCompanyTree($company, $companies);
      }
    }

    echo json_encode($result, JSON_PRETTY_PRINT);

    echo 'Total time: ' . (microtime(true) - $start);
  }

  private function buildCompanyTree($company, $companies)
  {
    $companyData = [
      'id' => $company->id,
      'createdAt' => $company->createdAt,
      'name' => $company->name,
      'parentId' => $company->parentId,
      'cost' => $company->cost,
      'children' => [],
    ];

    foreach ($companies as $childCompany) {
      if ($childCompany->parentId === $company->id) {
        $companyData['children'][] = $this->buildCompanyTree($childCompany, $companies);
      }
    }

    return $companyData;
  }

  private function findCompanyById($companies, $companyId)
  {
    foreach ($companies as $company) {
      if ($company->id === $companyId) {
        return $company;
      }
    }

    return null;
  }
}

(new TestScript())->execute();

//[
//  {
//    "id": "uuid-1",
//    "createdAt": "2021-02-26T00:55:36.632Z",
//    "name": "Webprovise Corp",
//    "parentId": "0",
//    "cost": 52983,
//    "children": [
//      {
//        "id": "uuid-2",
//        "createdAt": "2021-02-25T10:35:32.978Z",
//        "name": "Stamm LLC",
//        "parentId": "uuid-1",
//        "cost": 5199,
//        "children": [
//          {
//            "id": "uuid-4",
//            "createdAt": "2021-02-25T06:11:47.519Z",
//            "name": "Price and Sons",
//            "parentId": "uuid-2",
//            "cost": 1340,
//            "children": []
//          },
//          {
//            "id": "uuid-7",
//            "createdAt": "2021-02-25T07:56:32.335Z",
//            "name": "Zieme - Mills",
//            "parentId": "uuid-2",
//            "cost": 1636,
//            "children": []
//          },
//          {
//            "id": "uuid-19",
//            "createdAt": "2021-02-25T21:06:18.777Z",
//            "name": "Schneider - Adams",
//            "parentId": "uuid-2",
//            "cost": 794,
//            "children": []
//          }
//        ]
//      },
//      {
//        "id": "uuid-3",
//        "createdAt": "2021-02-25T15:16:30.887Z",
//        "name": "Blanda, Langosh and Barton",
//        "parentId": "uuid-1",
//        "cost": 15713,
//        "children": [
//          {
//            "id": "uuid-5",
//            "createdAt": "2021-02-25T13:35:57.923Z",
//            "name": "Hane - Windler",
//            "parentId": "uuid-3",
//            "cost": 1288,
//            "children": []
//          },
//          {
//            "id": "uuid-6",
//            "createdAt": "2021-02-26T01:41:06.479Z",
//            "name": "Vandervort - Bechtelar",
//            "parentId": "uuid-3",
//            "cost": 2512,
//            "children": []
//          },
//          {
//            "id": "uuid-9",
//            "createdAt": "2021-02-25T16:02:49.099Z",
//            "name": "Kuhic - Swift",
//            "parentId": "uuid-3",
//            "cost": 3086,
//            "children": []
//          },
//          {
//            "id": "uuid-17",
//            "createdAt": "2021-02-25T11:17:52.132Z",
//            "name": "Rohan, Mayer and Haley",
//            "parentId": "uuid-3",
//            "cost": 4072,
//            "children": []
//          },
//          {
//            "id": "uuid-20",
//            "createdAt": "2021-02-26T01:51:25.421Z",
//            "name": "Kunde, Armstrong and Hermann",
//            "parentId": "uuid-3",
//            "cost": 908,
//            "children": []
//          }
//        ]
//      },
//      {
//        "id": "uuid-8",
//        "createdAt": "2021-02-25T23:47:57.596Z",
//        "name": "Bartell - Mosciski",
//        "parentId": "uuid-1",
//        "cost": 28817,
//        "children": [
//          {
//            "id": "uuid-10",
//            "createdAt": "2021-02-26T01:39:33.438Z",
//            "name": "Lockman Inc",
//            "parentId": "uuid-8",
//            "cost": 4288,
//            "children": []
//          },
//          {
//            "id": "uuid-11",
//            "createdAt": "2021-02-26T00:32:01.307Z",
//            "name": "Parker - Shanahan",
//            "parentId": "uuid-8",
//            "cost": 12236,
//            "children": [
//              {
//                "id": "uuid-12",
//                "createdAt": "2021-02-25T06:44:56.245Z",
//                "name": "Swaniawski Inc",
//                "parentId": "uuid-11",
//                "cost": 2110,
//                "children": []
//              },
//              {
//                "id": "uuid-14",
//                "createdAt": "2021-02-25T15:22:08.098Z",
//                "name": "Weimann, Runolfsson and Hand",
//                "parentId": "uuid-11",
//                "cost": 7254,
//                "children": []
//              }
//            ]
//          },
//          {
//            "id": "uuid-13",
//            "createdAt": "2021-02-25T20:45:53.518Z",
//            "name": "Balistreri - Bruen",
//            "parentId": "uuid-8",
//            "cost": 1686,
//            "children": []
//          },
//          {
//            "id": "uuid-15",
//            "createdAt": "2021-02-25T18:00:26.864Z",
//            "name": "Predovic and Sons",
//            "parentId": "uuid-8",
//            "cost": 4725,
//            "children": []
//          },
//          {
//            "id": "uuid-16",
//            "createdAt": "2021-02-26T01:50:50.354Z",
//            "name": "Weissnat - Murazik",
//            "parentId": "uuid-8",
//            "cost": 3277,
//            "children": []
//          }
//        ]
//      },
//      {
//        "id": "uuid-18",
//        "createdAt": "2021-02-26T02:31:22.154Z",
//        "name": "Walter, Schmidt and Osinski",
//        "parentId": "uuid-1",
//        "cost": 2033,
//        "children": []
//      }
//    ]
//  }
//]
//Total time: 1.5756630897522