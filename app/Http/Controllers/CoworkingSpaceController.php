<?php

namespace App\Http\Controllers;

use App\Models\CoworkingSpaces;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API de Gestion des Espaces de Coworking",
 *     version="1.0.0",
 *     description="Cette API permet de gérer les espaces de coworking, y compris la création, la lecture, la mise à jour, et la suppression des espaces."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\Schema(
 *     schema="CoworkingSpace",
 *     type="object",
 *     title="Coworking Space",
 *     description="Un espace de coworking",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID de l'espace de coworking"),
 *     @OA\Property(property="name", type="string", description="Nom de l'espace de coworking"),
 *     @OA\Property(property="address", type="string", description="Adresse de l'espace de coworking"),
 *     @OA\Property(property="city", type="string", description="Ville de l'espace de coworking"),
 *     @OA\Property(property="country", type="string", description="Pays de l'espace de coworking"),
 *     @OA\Property(property="description", type="string", description="Description de l'espace de coworking"),
 *     @OA\Property(property="price_per_hour", type="number", format="float", description="Prix par heure")
 * )
 */
class CoworkingSpaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/coworking-spaces",
     *     tags={"Coworking Spaces"},
     *     summary="Get all coworking spaces",
     *     description="Returns a list of all coworking spaces",
     *     @OA\Response(
     *         response=200,
     *         description="List of coworking spaces",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CoworkingSpace")),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $coworkingSpaces = CoworkingSpaces::all();
            return response()->json([
                "data" => $coworkingSpaces,
                "total" => $coworkingSpaces->count(),
                "message" => $coworkingSpaces->isEmpty() ? "Aucun espace de coworking pour le moment" : "Espace de coworking disponible",
                "status" => "success",
                "code" => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des espaces de coworking",
                "error" => $th->getMessage(),
                "status" => "error",
                "code" => 500
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/coworking-spaces/{id}",
     *     tags={"Coworking Spaces"},
     *     summary="Get a coworking space by ID",
     *     description="Returns a single coworking space",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coworking space details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/CoworkingSpace"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $coworkingSpace = CoworkingSpaces::findOrFail($id);
            return response()->json([
                "data" => $coworkingSpace,
                "message" => "Espace de coworking récupéré avec succès",
                "status" => "success",
                "code" => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Espace de coworking non trouvé",
                "error" => $e->getMessage(),
                "status" => "error",
                "code" => 404
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération de l'espace de coworking",
                "error" => $th->getMessage(),
                "status" => "error",
                "code" => 500
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/coworking-spaces/{id}",
     *     tags={"Coworking Spaces"},
     *     summary="Update a coworking space by ID",
     *     description="Updates a coworking space with the given ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="address", type="string", maxLength=255),
     *             @OA\Property(property="city", type="string", maxLength=255),
     *             @OA\Property(property="country", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price_per_hour", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coworking space updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/CoworkingSpace"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'sometimes|numeric',
        ]);

        try {
            $coworkingSpace = CoworkingSpaces::findOrFail($id);
            $coworkingSpace->update($validatedData);

            return response()->json([
                "data" => $coworkingSpace,
                "message" => "Espace de coworking mis à jour avec succès"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Espace de coworking non trouvé",
                "error" => $e->getMessage()
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la mise à jour de l'espace de coworking",
                "error" => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/coworking-spaces/{id}",
     *     tags={"Coworking Spaces"},
     *     summary="Delete a coworking space by ID",
     *     description="Deletes a coworking space with the given ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Coworking space deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $coworkingSpace = CoworkingSpaces::findOrFail($id);
            $coworkingSpace->delete();

            return response()->json([
                "message" => "Espace de coworking supprimé avec succès"
            ], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Espace de coworking non trouvé",
                "error" => $e->getMessage()
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la suppression de l'espace de coworking",
                "error" => $th->getMessage()
            ], 500);
        }
    }
}
