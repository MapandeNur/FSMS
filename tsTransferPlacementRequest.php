[1mdiff --git a/backend/app/Http/Controllers/PlacementController.php b/backend/app/Http/Controllers/PlacementController.php[m
[1mindex eba2306..b136398 100644[m
[1m--- a/backend/app/Http/Controllers/PlacementController.php[m
[1m+++ b/backend/app/Http/Controllers/PlacementController.php[m
[36m@@ -4,15 +4,17 @@[m
 [m
 use App\Http\Requests\EndPlacementRequest;[m
 use App\Http\Requests\StorePlacementRequest;[m
[31m-use App\Http\Requests\TransferPlacementRequest;[m
 use App\Http\Resources\PlacementResource;[m
 use App\Models\Placement;[m
 use App\Models\Student;[m
 use App\Services\PlacementService;[m
 use Illuminate\Http\JsonResponse;[m
[32m+[m[32muse Illuminate\Http\Request;[m
 [m
 class PlacementController extends Controller[m
 {[m
[32m+[m[32m    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;[m
[32m+[m
     public function __construct([m
         private PlacementService $placementService[m
     ) {[m
[36m@@ -46,13 +48,13 @@[m [mpublic function store(StorePlacementRequest $request, Student $student): JsonRes[m
         ], 201);[m
     }[m
 [m
[31m-    public function end(EndPlacementRequest $request, Placement $placement): JsonResponse[m
[32m+[m[32m    public function end(Request $request, Placement $placement): JsonResponse[m
     {[m
         $this->authorize('end', $placement);[m
 [m
         $placement = $this->placementService->end([m
             $placement,[m
[31m-            $request->validated()['reason'][m
[32m+[m[32m            $request->validate(['reason' => ['required', 'string']])['reason'][m
         );[m
 [m
         return response()->json([[m
[36m@@ -62,7 +64,7 @@[m [mpublic function end(EndPlacementRequest $request, Placement $placement): JsonRes[m
         ]);[m
     }[m
 [m
[31m-    public function transfer(TransferPlacementRequest $request, Placement $placement): JsonResponse[m
[32m+[m[32m    public function transfer(StorePlacementRequest $request, Placement $placement): JsonResponse[m
     {[m
         $this->authorize('transfer', $placement);[m
 [m
