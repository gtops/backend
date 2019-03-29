import { IRoutes } from "../core/routes/interfaces/IRoutes";
import authorization from "./authorization";
import participant from "./participant";

export const routes: IRoutes = {
	...participant,
	...authorization
};
