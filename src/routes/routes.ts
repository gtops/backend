import { IRoutes } from "../core/routes/interfaces/IRoutes";
import authorization from "./authorization";
import calculation from "./calculation";
import participant from "./participant";

export const routes: IRoutes = {
	...participant,
	...authorization,
	...calculation
};
