<?php

namespace App\Http\Controllers;

use App\Models\Reservations;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;

/**
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\Schema(
 *     schema="Reservation",
 *     type="object",
 *     title="Réservation",
 *     description="Une réservation",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID de la réservation"),
 *     @OA\Property(property="coworking_space_id", type="integer", description="ID de l'espace de coworking"),
 *     @OA\Property(property="user_id", type="integer", description="ID de l'utilisateur"),
 *     @OA\Property(property="start_time", type="string", format="date-time", description="Heure de début de la réservation"),
 *     @OA\Property(property="end_time", type="string", format="date-time", description="Heure de fin de la réservation")
 * )
 */

class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/reservations",
     *     tags={"Réservations"},
     *     summary="Get all reservations",
     *     description="Returns a list of all reservations",
     *     @OA\Response(
     *         response=200,
     *         description="List of reservations",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Reservation")),
     *             @OA\Property(property="size", type="integer"),
     *             @OA\Property(property="message", type="string")
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
    public function index()
    {
        try {
            $reservations = Reservations::all();
            return response()->json([
                "data" => $reservations,
                "size" => $reservations->count(),
                "message" => $reservations->isEmpty() ? "Aucune réservation pour le moment" : "Réservations disponibles"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des réservations",
                "error" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reservations/{id}",
     *     tags={"Réservations"},
     *     summary="Get a reservation by ID",
     *     description="Returns a single reservation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Reservation"),
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
    public function show($id)
    {
        try {
            $reservation = Reservations::findOrFail($id);
            return response()->json([
                "data" => $reservation,
                "message" => "Réservation récupérée avec succès"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Réservation non trouvée",
                "error" => $e->getMessage(),
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération de la réservation",
                "error" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reservations",
     *     tags={"Réservations"},
     *     summary="Create a new reservation",
     *     description="Creates a new reservation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="coworking_space_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="start_time", type="string", format="date-time"),
     *             @OA\Property(property="end_time", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservation created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Reservation"),
     *             @OA\Property(property="message", type="string")
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'coworking_space_id' => 'required|exists:coworking_spaces,id',
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        try {
            $reservation = Reservations::create($validatedData);
            return response()->json([
                "data" => $reservation,
                "message" => "Réservation créée avec succès"
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la création de la réservation",
                "error" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/reservations/{id}",
     *     tags={"Réservations"},
     *     summary="Update a reservation by ID",
     *     description="Updates a reservation with the given ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="start_time", type="string", format="date-time"),
     *             @OA\Property(property="end_time", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Reservation"),
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
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
        ]);

        try {
            $reservation = Reservations::findOrFail($id);
            $reservation->update($validatedData);
            return response()->json([
                "data" => $reservation,
                "message" => "Réservation mise à jour avec succès"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Réservation non trouvée",
                "error" => $e->getMessage(),
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la mise à jour de la réservation",
                "error" => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/reservations/{id}",
     *     tags={"Réservations"},
     *     summary="Delete a reservation by ID",
     *     description="Deletes a reservation with the given ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
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
    public function destroy($id)
    {
        try {
            $reservation = Reservations::findOrFail($id);
            $reservation->delete();
            return response()->json([
                "message" => "Réservation supprimée avec succès"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "Réservation non trouvée",
                "error" => $e->getMessage(),
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la suppression de la réservation",
                "error" => $th->getMessage(),
            ], 500);
        }
    }
}
